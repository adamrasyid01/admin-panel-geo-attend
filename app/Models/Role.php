<?php

namespace App\Models;

// Impor model Role dari paket Spatie
use Spatie\Permission\Models\Role as SpatieRole;

// Impor trait jika Anda ingin menambahkannya, seperti SoftDeletes
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Tambahkan trait di sini jika diperlukan.
    // Catatan: Spatie sudah mengurus HasFactory
    // Jadi Anda hanya perlu menambahkan SoftDeletes jika Anda menggunakannya
    use SoftDeletes;

    public function users() : HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

}