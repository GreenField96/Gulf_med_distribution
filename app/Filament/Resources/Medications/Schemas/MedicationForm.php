<?php

namespace App\Filament\Resources\Medications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('medical_name')
                    ->label(__('Medical Name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('dosages')
                    ->label(__('Dosages'))
                    ->placeholder('e.g., 1x Daily, 2x Daily')
                    ->default('1*')
                    ->required()
                    ->maxLength(100),

                TextInput::make('price')
                    ->label(__('Price'))
                    ->placeholder('e.g., 10.5, 20.0')
                    ->numeric()
                    ->step(0.01) // Allows decimal inputs up to 2 decimal places (adjust as needed)
                    ->minValue(0)
                    ->required(),
                    ]);
    }
}
