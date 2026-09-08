<?php

namespace App\Filament\Resources\MonthlyTrackings\MonthlyTrackings\Pages;

use App\Filament\Resources\MonthlyTrackings\MonthlyTrackings\MonthlyTrackingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyTracking extends EditRecord
{
    protected static string $resource = MonthlyTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
