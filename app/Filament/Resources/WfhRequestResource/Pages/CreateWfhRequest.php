<?php

namespace App\Filament\Resources\WfhRequestResource\Pages;

use App\Filament\Resources\WfhRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWfhRequest extends CreateRecord
{
    protected static string $resource = WfhRequestResource::class;

    // Mengisi status awal pending saat admin membuat pengajuan WFH dari Filament.
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';

        return $data;
    }
}
