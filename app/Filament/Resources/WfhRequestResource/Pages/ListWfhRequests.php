<?php

namespace App\Filament\Resources\WfhRequestResource\Pages;

use App\Filament\Resources\WfhRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWfhRequests extends ListRecords
{
    protected static string $resource = WfhRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
