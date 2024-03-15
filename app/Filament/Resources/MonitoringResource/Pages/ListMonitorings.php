<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use Filament\Actions;
use App\Models\Monitoring;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\MonitoringResource;

class ListMonitorings extends ListRecords
{
    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\CreateAction::make(),
        ];
    }
}
