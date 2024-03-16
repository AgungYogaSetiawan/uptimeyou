<?php

namespace App\Filament\Resources\MonitoringResource\Widgets;

use App\Models\Result;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\MonitoringResource;

class MonitoringChart extends ChartWidget
{
    protected static ?string $heading = 'Monitoring Graphics Performance Website';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    public ?Model $record = null;

    protected function getData(): array
    {
        $getResponseTime = Result::where('monitoring_id', $this->record->getOriginal('id'))->pluck('response_time', 'created_at')->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Response Time',
                    'data' => array_values($getResponseTime),
                ],
            ],
            'labels' => array_keys($getResponseTime),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
