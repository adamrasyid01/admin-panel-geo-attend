<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Attendance extends Model
{
    //
    protected $fillable = [
        'user_id',
        'photo',
        'user_shift_id',
        'check_in_time',
        'check_out_time',
        'check_in_location',
        'check_out_location'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'check_in_location' => 'array',
        'check_out_location' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userShift(): BelongsTo
    {
        return $this->belongsTo(UserShift::class, 'user_shift_id');
    }
    public function setCheckInTimeAttribute($value)
    {
        $this->attributes['check_in_time'] = $value;

        if ($this->userShift) {
            $jadwalMasuk = $this->userShift->shift->start_time;
            $waktuCheckIn = (new \DateTime($value))->format('H:i:s');

            if ($waktuCheckIn <= $jadwalMasuk) {
                $this->attributes['status'] = 'Tepat Waktu';
            } else {
                $this->attributes['status'] = 'Terlambat';
            }
        } else {
            $this->attributes['status'] = 'N/A';
        }
    }
}
