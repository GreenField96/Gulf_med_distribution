<?php
namespace App\Filament\Resources\Members\Schemas;

use App\Filament\Resources\Members\Pages\CreateMember;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make([
                Select::make('company_id')
                    ->label(__('Company'))
                    ->relationship('company', 'company_name')
                    ->required()
                    ->default(fn () => auth()->user()?->company_id)
                    ->disabled(fn () => auth()->user()?->isCompanyMember())
                    ->dehydrated(), 

                TextInput::make('first_name')
                    ->label(__('Name'))
                    ->required(),

                TextInput::make('last_name')
                    ->label(__('Last Name'))
                    ->required(),

                TextInput::make('member_ID')
                    ->label(__('Member ID'))
                    ->required()
                    ->unique(ignoreRecord: true),

            ])->disabled(fn () => ! in_array(auth()->user()?->role, ['admin_user', 'companies_members'])), // <-- Chained to Group::make()
                
            Section::make([
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
            ])->disabled(fn () => ! in_array(auth()->user()?->role, ['admin_user', 'companies_members'])), // <-- Chained to Group::make()
        
        ]);
    }
}