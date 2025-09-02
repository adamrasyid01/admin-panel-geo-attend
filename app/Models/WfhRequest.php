<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WfhRequest extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'tanggal',
        'reason',
        'status',
        'approved_by',
        'admin_notes'
    ];

public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
