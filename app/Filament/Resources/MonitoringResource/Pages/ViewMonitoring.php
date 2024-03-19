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
                    $monitoring::where('id', $this->record->getOriginal('id'));
                    if ($monitoring->status == 'active') {
                        $monitoring->update(['status' => 'pause']);
                        Notification::make()
                            ->title('Pause successfully')
                            ->success()
                            ->send();
                    } else {
                        $monitoring->update(['status' => 'active']);
                        Notification::make()
                            ->title('Resumed successfully')
                            ->success()
                            ->send();
                    }
                })
                ->label(function (Monitoring $monitoring): string {
                    $monitoring::where('id', $this->record->getOriginal('id'));
                    if ($monitoring->status == 'pause')
                        return 'Play';
                    else {
                        return 'Pause';
                    }
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
