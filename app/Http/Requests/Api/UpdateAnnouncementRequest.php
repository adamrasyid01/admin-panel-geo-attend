<?php

namespace App\Http\Requests\Api;

class UpdateAnnouncementRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'attachment_path' => 'nullable|file|mimes:pdf|max:1024',
            'is_published' => 'sometimes|boolean',
        ];
    }
}
