<?php

namespace App\Http\Requests\Api;

class UpdateShiftRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i',
        ];
    }
}
