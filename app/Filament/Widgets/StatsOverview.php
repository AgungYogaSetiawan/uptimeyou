<?php

namespace App\Filament\Widgets;

use App\Models\Monitoring;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active', Monitoring::where('status', 'active')->where('user_id', Auth::user()->id)->count())
                ->description('Status Active Website')
                ->color('success'),
            Stat::make('Not Active', Monitoring::where('status', 'not active')->where('user_id', Auth::user()->id)->count())
                ->description('Status Not Active Website')
                ->color('danger'),
            Stat::make('Pause', Monitoring::where('status', 'pause')->where('user_id', Auth::user()->id)->count())
                ->description('Status Pause Website')
                ->color('secondary'),
        ];
    }
}
