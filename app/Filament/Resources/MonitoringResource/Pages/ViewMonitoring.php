<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use App\Filament\Resources\MonitoringResource;
use App\Filament\Resources\MonitoringResource\Widgets\MonitoringChart;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoring extends ViewRecord
{
    protected static string $resource = MonitoringResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            MonitoringChart::class,
        ];
    }
}
