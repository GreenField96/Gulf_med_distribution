<?php

namespace App\Filament\Resources\MonthlyTrackings;

use App\Filament\Resources\MonthlyTrackings\Pages;
use App\Models\DateWhenMemberTakeHisMedical;
use App\Models\Member;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Tcpdf\Fpdi;
use Mpdf\Mpdf;

use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use ZipArchive;

use App\Models\MonthlyTracking;

class MonthlyTrackingResource extends Resource
{
    protected static ?string $model = DateWhenMemberTakeHisMedical::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function canViewAny(): bool
    {
        return auth()->check();
    }
    
    // public static function canCreate(): bool
    // {
    //     return false;
    // }

    public static function getNavigationLabel(): string
    {
        return __('Monthly Tracking');
    }

    public static function getModelLabel(): string
    {
        return __('Monthly Tracking');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Monthly Trackings');
    }
    


    public static function getEloquentQuery(): Builder
    {
        $currentMonth = Carbon::now()->startOfMonth()->toDateString();
        static::ensureMonthlyRecordsExist($currentMonth);

        // Eager load the member relationship to check 'is_locked' efficiently
        return parent::getEloquentQuery()->with('member');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_taken')
                    ->label(__('Is Taken'))
                    ->default(false)
                    ->disabled(fn ($record) => $record?->member?->is_locked ?? false),

                Select::make('confirmed_by_user_id')
                    ->label(__('Confirmed By'))
                    ->relationship('confirmedBy', 'name')
                    ->searchable()
                    ->default(fn () => auth()->id()),
            ]);
    }


public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.first_name')
                    ->label(__('First Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('member.last_name')
                    ->label(__('Last Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('member.member_ID')
                    ->label(__('Member ID'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('member.national_ID')
                    ->label(__('National ID'))
                    ->searchable()
                    ->default('-'),

                // Interactive Checkbox Column
                Tables\Columns\CheckboxColumn::make('is_taken')
                    ->label(__('Medical Taken'))
                    ->disabled(function ($record) {
                        /** @var \App\Models\User $user */
                        $user = auth()->user();

                        // 1. Disable if current user is not authorized (Must be Admin or Medical Operator)
                        if (!$user || !($user->isAdmin() || $user->isMedicalOperator())) {
                            return true;
                        }

                        if (!$record || !$record->date_month_year) {
                            return true;
                        }

                        // 2. Disable if member is locked due to PDF/document change
                        if ($record->member && $record->member->is_locked) {
                            return true;
                        }

                        $selectedMonth = Carbon::parse($record->date_month_year)->startOfMonth();
                        $currentMonth = Carbon::now()->startOfMonth();

                        // 3. Lock modifications if the record belongs to a past month
                        return $selectedMonth->lt($currentMonth);
                    })
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->update([
                            'confirmed_by_user_id' => $state ? auth()->id() : null,
                        ]);
                    }),

                // Lock Warning Note Column
                Tables\Columns\TextColumn::make('lock_status')
                    ->label(__('Note'))
                    ->getStateUsing(function ($record) {
                        if ($record->member && $record->member->is_locked) {
                            return __('Please adjust Member medications');
                        }
                        return null;
                    })
                    ->badge()
                    ->color('danger')
                    ->wrap(),

                Tables\Columns\TextColumn::make('confirmedBy.name')
                    ->label(__('Confirmed By'))
                    ->default('-'),
            ])
            
            ->filters([
                Tables\Filters\SelectFilter::make('date_month_year')
                    ->label(__('Select Month'))
                    ->options(function () {
                        return DateWhenMemberTakeHisMedical::query()
                            ->selectRaw("DATE_FORMAT(date_month_year, '%Y-%m-01') as month_val, DATE_FORMAT(date_month_year, '%m/%Y') as month_label")
                            ->distinct()    
                            ->orderBy('month_val', 'desc')
                            ->pluck('month_label', 'month_val')
                            ->toArray();
                    })
                    ->default(Carbon::now()->startOfMonth()->toDateString())
                    ->selectablePlaceholder(false)
                    ->query(function (Builder $query, array $data) {
                        $selectedMonth = !empty($data['value'])
                            ? $data['value']
                            : Carbon::now()->startOfMonth()->toDateString();

                        // Seed records specifically for the filtered month
                        static::ensureMonthlyRecordsExist($selectedMonth);

                        return $query->whereDate('date_month_year', $selectedMonth);
                    }),
            ])
            
            ->actions([
                Action::make('generateMergedPdf')
            ->label(__('Print PDF'))
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->visible(fn ($record) => (bool) $record->is_taken)
            ->action(function ($record) {
        $member = $record->member;

        if (!$member) {
            return;
        }

        $medications = $member->medications ?? collect();
        // $totalPrice = $medications->sum('price') ?? 0;
        $totalPrice = $medications->sum(function ($med) {
            $amount = $med->pivot->amount ?? 1;
            return $med->price * $amount;
        });

        // 1. Render Blade HTML View
        $html = view('pdf.invoice', [
            'member' => $member,
            'invoice_no' => 'INV-' . $record->id,
            'date' => Carbon::parse($record->date_month_year)->format('Y/m'),
            'medications' => $medications,
            'total_price' => $totalPrice,
        ])->render();

// dd($member->medications);

        // 2. Initialize mPDF with Arabic RTL Support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'autoScriptToLang' => true,  // Automatically detects Arabic scripts
            'autoLangToFont'   => true,  // Automatically applies Arabic-compatible fonts
        ]);

        // Force Right-To-Left direction for the entire document
        $mpdf->SetDirectionality('rtl');

        // Write HTML and output temp file
        $mpdf->WriteHTML($html);
        $invoicePath = storage_path("app/temp_invoice_{$record->id}.pdf");
        $mpdf->Output($invoicePath, \Mpdf\Output\Destination::FILE);

        // 3. Locate existing Member PDF
        $memberPdfRelativePath = $member->pdf_file_path;
        $memberPdfFullPath = $memberPdfRelativePath ? Storage::disk('public')->path($memberPdfRelativePath) : null;

        // 4. Merge PDFs using FPDI
        $fpdi = new Fpdi();
        $fpdi->setPrintHeader(false);
        $fpdi->setPrintFooter(false);

        // Import Generated Invoice Pages
        if (file_exists($invoicePath)) {
            $pageCount = $fpdi->setSourceFile($invoicePath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($template);
            }
        }

        // Import Member Attachment Pages (if exists)
        if ($memberPdfFullPath && file_exists($memberPdfFullPath)) {
            $memberPageCount = $fpdi->setSourceFile($memberPdfFullPath);
            for ($i = 1; $i <= $memberPageCount; $i++) {
                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($template);
            }
        }

        // Output merged content string
        $mergedPdfContent = $fpdi->Output('', 'S');

        // Clean up temporary invoice file
        if (file_exists($invoicePath)) {
            unlink($invoicePath);
        }

        // 5. Trigger stream download in browser
        $filename = "{$member->member_ID} {$totalPrice}.pdf";

        return response()->streamDownload(
            fn () => print($mergedPdfContent),
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    })
    
    
        ])->headerActions([
            Action::make('downloadAllTakenInvoices')
                ->label('تحميل كافة الفواتير المستلمة (ZIP)')
                ->icon('heroicon-o-document-duplicate')
                ->color('success')
                ->action(function () {
                    $takenRecords = MonthlyTracking::with(['member.medications'])
                        ->where('is_taken', true)
                        ->get();

                    if ($takenRecords->isEmpty()) {
                        return;
                    }

                    $zip = new ZipArchive();
                    $zipFileName = 'invoices_' . now()->format('Y-m-d_H-i-s') . '.zip';
                    $zipPath = storage_path("app/public/{$zipFileName}");

                    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                        return;
                    }

                    // 2. Process each taken record
                    foreach ($takenRecords as $record) {
                        $member = $record->member;
                        if (!$member) {
                            continue;
                        }

                        $medications = $member->medications;

                        // Calculate total price: unit price * pivot amount
                        $totalPrice = $medications->sum(function ($med) {
                            $amount = $med->pivot->amount ?? 1;
                            return $med->price * $amount;
                        });

                        // Render invoice HTML template
                        $html = view('pdf.invoice', [
                            'member' => $member,
                            'invoice_no' => 'INV-' . $record->id,
                            'date' => Carbon::parse($record->date_month_year)->format('Y/m'),
                            'medications' => $medications,
                            'total_price' => $totalPrice,
                        ])->render();

                        // Initialize mPDF for Arabic RTL support
                        $mpdf = new Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 10,
                            'margin_bottom' => 10,
                            'autoScriptToLang' => true,
                            'autoLangToFont'   => true,
                        ]);
                        $mpdf->SetDirectionality('rtl');
                        $mpdf->WriteHTML($html);

                        // 3. Resolve path to member's document in storage/app/private/medical_pdfs
                        $memberPdfPath = null;
                        $pdfFile = $member->reference_to_pdf;

                        if (!empty($pdfFile)) {
                            if (Storage::disk('local')->exists($pdfFile)) {
                                $memberPdfPath = Storage::disk('local')->path($pdfFile);
                            } elseif (file_exists(storage_path('app/private/' . $pdfFile))) {
                                $memberPdfPath = storage_path('app/private/' . $pdfFile);
                            } elseif (file_exists(storage_path('app/private/medical_pdfs/' . basename($pdfFile)))) {
                                $memberPdfPath = storage_path('app/private/medical_pdfs/' . basename($pdfFile));
                            }
                        }

                        // 4. Import & append member's existing PDF pages directly into mPDF
                        if ($memberPdfPath && file_exists($memberPdfPath)) {
                            try {
                                $pageCount = $mpdf->setSourceFile($memberPdfPath);
                                for ($i = 1; $i <= $pageCount; $i++) {
                                    $mpdf->AddPage();
                                    $templateId = $mpdf->importPage($i);
                                    $mpdf->useTemplate($templateId);
                                }
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to append member PDF (Member ID {$member->id}): " . $e->getMessage());
                            }
                        }

                        // 5. Save merged single PDF document into the ZIP file
                        $mergedContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
                        $filenameInZip = "{$member->member_ID} {$totalPrice}.pdf";

                        $zip->addFromString($filenameInZip, $mergedContent);
                    }

                    $zip->close();

                    return response()->download($zipPath)->deleteFileAfterSend(true);
                }),
        ]);

            
    }

    /**
     * Bulk inserts tracking entries for all members missing a record in $monthDate.
     */
    public static function ensureMonthlyRecordsExist(string $monthDate): void
    {
        $existingMemberIds = DateWhenMemberTakeHisMedical::query()
            ->whereDate('date_month_year', $monthDate)
            ->pluck('member_id')
            ->toArray();

        $missingMemberIds = Member::query()
            ->whereNotIn('id', $existingMemberIds)
            ->pluck('id');

        if ($missingMemberIds->isEmpty()) {
            return;
        }

        $now = now();
        $recordsToInsert = [];

        foreach ($missingMemberIds as $memberId) {
            $recordsToInsert[] = [
                'member_id'            => $memberId,
                'date_month_year'      => $monthDate,
                'is_taken'             => false,
                'confirmed_by_user_id' => null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];
        }

        // Chunk insertions for large datasets to prevent MySQL packet limit overflow
        foreach (array_chunk($recordsToInsert, 500) as $chunk) {
            DateWhenMemberTakeHisMedical::insert($chunk);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyTrackings::route('/'),
        ];
    }
}