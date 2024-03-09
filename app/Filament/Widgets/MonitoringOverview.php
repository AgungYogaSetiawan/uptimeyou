<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Monitoring;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MonitoringResource;
use Filament\Widgets\TableWidget as BaseWidget;

class MonitoringOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full'; // untuk merubah width widget
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                MonitoringResource::getEloquentQuery()
            )
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $userId = Auth::user()->id;
                $query->where('user_id', $userId);
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status_code'),
            ]);
    }
}
