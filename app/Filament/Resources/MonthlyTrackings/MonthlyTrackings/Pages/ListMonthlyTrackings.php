<?php

namespace App\Filament\Resources\MonthlyTrackings\MonthlyTrackings\Pages;

use App\Filament\Resources\MonthlyTrackings\MonthlyTrackings\MonthlyTrackingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMonthlyTrackings extends ListRecords
{
    protected static string $resource = MonthlyTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
