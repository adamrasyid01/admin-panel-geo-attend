<?php

namespace App\Filament\Resources\WfhRequestResource\Pages;

use App\Filament\Resources\WfhRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditWfhRequest extends EditRecord
{
    protected static string $resource = WfhRequestResource::class;

    // Menyelaraskan approved_by dan notes_by dengan status serta catatan admin saat menyimpan.
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['approved_by'] = ($data['status'] ?? null) === 'approved' ? Auth::id() : null;
        $data['notes_by'] = filled($data['admin_notes'] ?? null) ? Auth::id() : null;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
