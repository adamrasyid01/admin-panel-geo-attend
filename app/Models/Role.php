<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
    ];

    /**
     * Get the users associated with the role.
     */
    public function users() : HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
    
}
