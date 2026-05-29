<?php

namespace App\Http\Requests\Api;

class CheckOutAttendanceRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'check_out_time' => 'required|date',
            'check_out_location' => 'required|string',
        ];
    }
}
