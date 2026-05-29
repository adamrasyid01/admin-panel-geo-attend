<?php

namespace App\Http\Requests\Api;

class UpdateNoteRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ];
    }
}
