<?php

namespace App\Http\Requests\Api;

class UpdateWfhRequest extends ApiRequest
{
    public function rules(): array
    {
        $rules = [
            'tanggal' => 'sometimes|required|date',
            'reason' => 'sometimes|required|string|max:255',
        ];

        if ($this->user()?->can('update_wfh::request') || $this->user()?->hasRole('super_admin')) {
            $rules['status'] = 'sometimes|required|string|in:pending,approved,rejected';
            $rules['admin_notes'] = 'nullable|string';
        }

        return $rules;
    }
}
