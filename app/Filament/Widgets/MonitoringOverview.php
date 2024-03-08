<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MonitoringResource;
use App\Models\Monitoring;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
