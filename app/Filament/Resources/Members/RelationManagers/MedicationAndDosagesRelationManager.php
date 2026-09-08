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

                TextInput::make('dosage')
                    ->label(__('Dosage'))
                    ->placeholder('e.g., 500mg - 2x daily')
                    ->required(),

                TextInput::make('price')
                    ->label(__('Price'))
                    ->numeric()
                    ->prefix('LYD')
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

                Tables\Columns\TextColumn::make('dosage')
                    ->label(__('Dosage')),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
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