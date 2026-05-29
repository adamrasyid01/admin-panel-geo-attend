<?php

namespace App\Filament\Resources\OvertimeRequestResource\Pages;

use App\Filament\Resources\OvertimeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditOvertimeRequest extends EditRecord
{
    protected static string $resource = OvertimeRequestResource::class;

    // Menyelaraskan field approved_by dengan status lembur yang dipilih admin saat menyimpan.
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['approved_by'] = ($data['status'] ?? null) === 'approved' ? Auth::id() : null;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
