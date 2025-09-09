<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CompanyLocation extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'location',
        'allowed_radius',
        'slug'
    ];

    protected $casts = [
        'location' => 'array',
    ];

    /**
     * Get the user that owns the company location.
     */
    public function owner() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

   public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_company', 'company_location_id', 'user_id');
    }

}
