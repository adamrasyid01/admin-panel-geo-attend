<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['user_id', 'title', 'content', 'attachment_path', 'is_published', 'created_by'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
