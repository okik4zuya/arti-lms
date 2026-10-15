<?php

namespace App\Filament\Resources\BroadcastResource\Pages;

use App\Filament\Resources\BroadcastResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBroadcast extends CreateRecord
{
    protected static string $resource = BroadcastResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
