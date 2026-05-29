<?php

namespace App\Http\Requests\Api;

class UpdateTaskRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'deadline' => 'sometimes|required|date',
        ];
    }
}
