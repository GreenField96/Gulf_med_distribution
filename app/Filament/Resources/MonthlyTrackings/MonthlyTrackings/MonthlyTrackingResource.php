<?php
namespace App\Filament\Resources\MonthlyTrackings;

use App\Filament\Resources\MonthlyTrackings\Pages;
use App\Models\MonthlyTracking;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;


class MonthlyTrackingResource extends Resource
{
    protected static ?string $model = MonthlyTracking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $recordTitleAttribute = 'distribution_date';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label(__('Member'))
                    ->relationship('member', 'first_name')
                    ->searchable()
                    ->required(),

                Select::make('medication_id')
                    ->label(__('Medication'))
                    ->relationship('medication', 'medical_name')
                    ->searchable()
                    ->required(),

                DatePicker::make('distribution_date')
                    ->label(__('Distribution Date'))
                    ->default(now())
                    ->required(),

                TextInput::make('quantity_dispensed')
                    ->label(__('Quantity Dispensed'))
                    ->numeric()
                    ->default(1)
                    ->required(),

                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => 'Pending',
                        'dispensed' => 'Dispensed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('dispensed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.first_name')
                    ->label(__('Member First Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('member.last_name')
                    ->label(__('Member Last Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('medication.medical_name')
                    ->label(__('Medication'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('distribution_date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity_dispensed')
                    ->label(__('Quantity')),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'dispensed',
                        'danger' => 'cancelled',
                    ]),
                    
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'dispensed' => 'Dispensed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])->bulkActions([
    BulkAction::make('exportPdf')
        ->label(__('Export Summary PDF'))
        ->icon('heroicon-o-document-arrow-down')
        ->action(function (Collection $records) {
            // Trigger PDF generation job or download response
        })
        ->visible(fn () => auth()->user()->isAdmin()),
]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyTrackings::route('/'),
            'create' => Pages\CreateMonthlyTracking::route('/create'),
            'edit' => Pages\EditMonthlyTracking::route('/{record}/edit'),
        ];
    }
}