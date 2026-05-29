<?php

namespace App\Http\Requests\Api;

class StoreNoteRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }
}
