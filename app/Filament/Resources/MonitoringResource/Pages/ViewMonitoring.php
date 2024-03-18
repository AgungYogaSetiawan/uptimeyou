<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\MonitoringResource;
use App\Filament\Resources\MonitoringResource\Widgets\MonitoringChart;
use App\Models\Monitoring;
use Filament\Notifications\Notification;

class ViewMonitoring extends ViewRecord
{
    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Pause')
                ->action(function (Monitoring $monitoring): void {
                    $monitoring->update(['pause' => 1]);
                    Notification::make()
                        ->title('Monitoring Paused')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MonitoringChart::class,
        ];
    }
}
