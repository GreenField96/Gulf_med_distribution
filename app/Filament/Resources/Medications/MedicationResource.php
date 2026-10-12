<?php
namespace App\Filament\Resources\Medications;

use App\Filament\Resources\Medications\Pages;
use App\Filament\Resources\Medications\Schemas\MedicationForm;
use App\Models\Medication;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;

class MedicationResource extends Resource
{
    protected static ?string $model = Medication::class;
    // protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    public static function getNavigationLabel(): string
    {
            return __('Medications');
    }
    public static function getModelLabel(): string
    {
        return __('Medication');
    }
        public static function getPluralModelLabel(): string
    {
        return __('Medications');
    }
    public static function form(Schema $schema): Schema
    {
        return MedicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medical_name')
                    ->label(__('Medical Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dosages')
                    ->label(__('Dosages'))
                    ->sortable(),

                    Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('LYD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedications::route('/'),
            'create' => Pages\CreateMedication::route('/create'),
            'edit' => Pages\EditMedication::route('/{record}/edit'),
        ];
    }
}