<?php

namespace App\Http\Requests\Api;

class CheckInAttendanceRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'user_shift_id' => 'required|exists:user_shifts,id',
            'photo' => 'required|string',
            'check_in_time' => 'required|date',
            'check_in_location' => 'required|string',
            'status' => 'required|string',
        ];
    }
}
