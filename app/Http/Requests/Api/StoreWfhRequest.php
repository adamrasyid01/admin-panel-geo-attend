<?php

namespace App\Http\Requests\Api;

class StoreWfhRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'reason' => 'required|string|max:255',
        ];
    }
}
