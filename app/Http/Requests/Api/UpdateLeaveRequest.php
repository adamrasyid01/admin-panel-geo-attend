<?php

namespace App\Http\Requests\Api;

class UpdateLeaveRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'type'=> 'sometimes|required|string|in:izin,cuti,sakit',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'sometimes|required|string|in:pending,approved,rejected',
        ];
    }
}
