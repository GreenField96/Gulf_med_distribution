<?php
namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Full Name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('Email Address'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('phone_num')
                    ->label(__('Phone Number'))
                    ->tel()
                    ->nullable(),

                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),

                Select::make('role')
                    ->label(__('Account Type / Role'))
                    ->options([
                        'admin_user' => 'System Administrator',
                        'medical_distro_operator_user' => 'Distribution Company Operator',
                        'companies_members' => 'Client Company Member',
                    ])
                    ->required()
                    ->reactive()
                    ->default('companies_members'),
                Select::make('locale')
    ->label(__('Preferred Language / اللغة المفضلة'))
    ->options([
        'ar' => 'العربية (Arabic)',
        'en' => 'English',
    ])
    ->default('ar')
    ->required(),
                Select::make('company_id')
                    ->label(__('Assigned Company'))
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(fn (callable $get) => $get('role') === 'companies_members')
                    ->visible(fn (callable $get) => $get('role') === 'companies_members')
                    ->dehydrated(),
            ]);
    }
}
