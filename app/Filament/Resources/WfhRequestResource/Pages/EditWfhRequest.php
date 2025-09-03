<?php

namespace App\Filament\Resources\WfhRequestResource\Pages;

use App\Filament\Resources\WfhRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWfhRequest extends EditRecord
{
    protected static string $resource = WfhRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
