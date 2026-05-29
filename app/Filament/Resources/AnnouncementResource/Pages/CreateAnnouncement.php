<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    // Mengisi otomatis admin pembuat pengumuman dan memastikan lampiran opsional aman disimpan.
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Secara otomatis mengisi kolom 'created_by' dengan ID user yang sedang login
        $data['created_by'] = Auth::id();
        $data['attachment_path'] = $data['attachment_path'] ?? '';

        return $data;
    }
}
