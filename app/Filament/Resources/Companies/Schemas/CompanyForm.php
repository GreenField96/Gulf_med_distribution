<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_name')
                    ->label(__('Company Name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone_num')
                    ->label(__('Phone Number'))
                    ->tel()
                    ->maxLength(50),
            ]);
    }
}