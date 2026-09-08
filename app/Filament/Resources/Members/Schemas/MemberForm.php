<?php
namespace App\Filament\Resources\Members\Schemas;

use App\Filament\Resources\Members\Pages\CreateMember;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'company_name')
                    ->label(__('Company'))
                    ->required()
                    ->default(fn () => auth()->user()->company_id ?? null)
                    ->disabled(fn () => auth()->user()->isCompanyMember()),

                TextInput::make('first_name')
                    ->label(__('First Name'))
                    ->required(),

                TextInput::make('last_name')
                    ->label(__('Last Name'))
                    ->required(),

                TextInput::make('member_ID')
                    ->label(__('Member ID'))
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('national_ID')
                    ->label(__('National ID'))
                    ->required(),

                TextInput::make('family_ID')
                    ->label(__('Family ID')),

                TextInput::make('phone_num')
                    ->label(__('Phone Number'))
                    ->tel(),

                FileUpload::make('reference_to_pdf')
                    ->label(__('Chronic Disease Document'))
                    ->directory('medical_pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->required(fn ($livewire) => $livewire instanceof CreateMember),
            ]);
    }
}