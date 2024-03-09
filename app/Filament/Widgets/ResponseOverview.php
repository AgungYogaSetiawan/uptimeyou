<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResponseOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full'; // untuk merubah width widget
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            // Stat::make('Response Time', '120 ms')
            //     ->description('Response Time Website'),
            // Stat::make('Avg Response Time', '110 ms')
            //     ->description('Avg Response Time Website'),
        ];
    }
}
