<?php

namespace App\Http\Requests\Api;

class StoreLeaveRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'type'=> 'required|string|in:izin,cuti,sakit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
