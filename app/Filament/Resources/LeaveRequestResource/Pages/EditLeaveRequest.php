<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    // Menyelaraskan field approved_by dengan status yang dipilih admin saat menyimpan.
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
