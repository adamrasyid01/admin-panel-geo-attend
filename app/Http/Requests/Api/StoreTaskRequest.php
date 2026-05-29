<?php

namespace App\Http\Requests\Api;

class StoreTaskRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
        ];
    }
}
