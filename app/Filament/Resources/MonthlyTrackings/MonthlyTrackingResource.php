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

use Illuminate\Support\Facades\Auth;

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
    
    
        ->headerActions([
            Action::make('downloadAllTakenInvoices')
                ->label('تحميل كافة الفواتير المستلمة (ZIP)')
                ->icon('heroicon-o-document-duplicate')
                ->color('success')
                ->visible(function () {
                    $user = Auth::user();
                    if (!$user) {
                        return false;
                    }

                    // Option B: If you have a 'role' column on your users table
                    return in_array($user->role, ['admin_user', 'medical_distro_operator_user']);
                })
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

            foreach ($takenRecords as $record) {
                $member = $record->member;
                if (!$member) {
                    continue;
                }

                $medications = $member->medications;

                // 1. Calculate Total Price
                $totalPrice = $medications->sum(function ($med) {
                    $amount = $med->pivot->amount ?? 1;
                    return $med->price * $amount;
                });

                // 2. Render Blade HTML View
                $html = view('pdf.invoice', [
                    'member' => $member,
                    'invoice_no' => 'INV-' . $record->id,
                    'date' => Carbon::parse($record->date_month_year)->format('Y/m'),
                    'medications' => $medications,
                    'total_price' => $totalPrice,
                ])->render();

                // 3. Initialize mPDF
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

                // 4. Resolve Private Member Document Path
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

                // 5. Append Member Attachment Pages to mPDF Invoice
                if ($memberPdfPath && file_exists($memberPdfPath)) {
                    try {
                        $pageCount = $mpdf->setSourceFile($memberPdfPath);
                        for ($i = 1; $i <= $pageCount; $i++) {
                            $mpdf->AddPage();
                            $templateId = $mpdf->importPage($i);
                            $mpdf->useTemplate($templateId);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to append member PDF (ID {$member->id}): " . $e->getMessage());
                    }
                }

                // 6. Generate Binary Content
                $mergedContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
                $filename = "{$member->member_ID} {$totalPrice}.pdf";

                // 7. Store Copy in Public Folder under Year-Month Directory (e.g. storage/app/public/2026-09/invoice 123 50.pdf)
                $monthFolder = Carbon::parse($record->date_month_year)->format('Y-m');
                $publicStoragePath = "{$monthFolder}/{$filename}";
                
                Storage::disk('public')->put($publicStoragePath, $mergedContent);

                // 8. Add File to Downloadable ZIP Archive
                $zip->addFromString($filename, $mergedContent);
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