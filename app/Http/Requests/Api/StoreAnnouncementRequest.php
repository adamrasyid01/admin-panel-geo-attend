<?php

namespace App\Http\Requests\Api;

class StoreAnnouncementRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:1024',
            'is_published' => 'sometimes|boolean',
        ];
    }
}
