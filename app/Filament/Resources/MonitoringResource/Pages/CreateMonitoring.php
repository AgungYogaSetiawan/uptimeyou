<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MonitoringResource;

class CreateMonitoring extends CreateRecord
{
    protected static string $resource = MonitoringResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
