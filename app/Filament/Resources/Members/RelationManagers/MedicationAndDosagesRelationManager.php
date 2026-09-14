<?php

namespace App\Filament\Resources\Members\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MedicationAndDosagesRelationManager extends RelationManager
{
    public static function getNavigationLabel(): string
    {
        return __('Medication And Dosages');
    }

    public static function getModelLabel(): string
    {
        return __('Medication And Dosage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Medication And Dosages');
    }

    protected static string $relationship = 'medicationAndDosages';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('medication_id')
                    ->label(__('Medication'))
                    ->relationship('medication', 'medical_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('amount')
                    ->label(__('Amount'))
                    ->placeholder('e.g., 1 , 2')
                    ->rule('gt:0')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dosage')
            ->columns([
                Tables\Columns\TextColumn::make('medication.medical_name')
                    ->label(__('Medication Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount')),

                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('Total Price'))
                    ->getStateUsing(function ($record) {
                        $unitPrice = $record->medication?->price ?? 0;
                        return $record->amount * $unitPrice;
                    })
                    ->money('LYD'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}