<?php


namespace App\Filament\Resources\MonthlyTrackings;

use App\Filament\Resources\MonthlyTrackings\Pages;
use App\Models\MonthlyTracking;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class MonthlyTrackingResource extends Resource
{
    protected static ?string $model = MonthlyTracking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function canViewAny(): bool
    {
        return auth()->check();
    }


public static function getNavigationLabel(): string
    {
    return __('Monthly Tracking');
    }
    public static function getModelLabel(): string
    {
    return __('Monthly Tracking');
    }
        public static function getPluralModelLabel(): string
    {
        return __('Monthly Trackings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label(__('Member'))
                    ->relationship('member', 'first_name')
                    ->searchable()
                    ->required(),

                DatePicker::make('date_month_year')
                    ->label(__('Date'))
                    ->default(now())
                    ->required(),

                Toggle::make('is_taken')
                    ->label(__('Is Taken'))
                    ->default(false),

                Select::make('confirmed_by_user_id')
                    ->label(__('Confirmed By'))
                    ->relationship('confirmedBy', 'name')
                    ->searchable()
                    ->default(fn () => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.first_name')
                    ->label(__('Member First Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('member.last_name')
                    ->label(__('Member Last Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_month_year')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_taken')
                    ->label(__('Taken'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('confirmedBy.name')
                    ->label(__('Confirmed By'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Recorded At'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_taken')
                    ->label(__('Medication Status'))
                    ->trueLabel(__('Taken'))
                    ->falseLabel(__('Not Taken')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlyTrackings::route('/'),
            'create' => Pages\CreateMonthlyTracking::route('/create'),
            'edit' => Pages\EditMonthlyTracking::route('/{record}/edit'),
        ];
    }
}