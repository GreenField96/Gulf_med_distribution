<?php
namespace App\Filament\Resources\Companies;

use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Models\Company;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    // protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('Company Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone_num')
                    ->label(__('Phone Number')),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}