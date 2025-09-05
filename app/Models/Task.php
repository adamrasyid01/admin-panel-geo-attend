<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'deadline',
        'created_by'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function createdTaskBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
