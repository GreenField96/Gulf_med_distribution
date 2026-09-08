<?php
namespace App\Filament\Resources\Members;

use App\Filament\Resources\Members\Pages;
use App\Filament\Resources\Members\Schemas\MemberForm;
use App\Models\Member;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use App\Filament\Resources\Members\RelationManagers\MedicationAndDosagesRelationManager;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    // protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    public static function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member_ID')->label(__('Member ID'))->searchable(),
                Tables\Columns\TextColumn::make('first_name')->label(__('First Name'))->searchable(),
                Tables\Columns\TextColumn::make('last_name')->label(__('Last Name'))->searchable(),
                Tables\Columns\TextColumn::make('company.company_name')->label(__('Company')),
                Tables\Columns\TextColumn::make('national_ID')->label(__('National ID')),
                Tables\Columns\IconColumn::make('reference_to_pdf')
                    ->label(__('Document'))
                    ->icon('heroicon-o-document-text')
                    ->url(fn ($record) => $record->reference_to_pdf ? asset('storage/' . $record->reference_to_pdf) : null, true),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            MedicationAndDosagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}