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
                    ->required()
                    ->maxLength(100),
            ]);
    }
}
