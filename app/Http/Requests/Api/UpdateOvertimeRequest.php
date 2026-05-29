<?php

namespace App\Http\Requests\Api;

use App\Models\OvertimeRequest;
use Illuminate\Validation\Validator;

class UpdateOvertimeRequest extends ApiRequest
{
    public function rules(): array
    {
        $rules = [
            'date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i',
            'reason' => 'nullable|string',
        ];

        if ($this->user()?->can('update_overtime::request') || $this->user()?->hasRole('super_admin')) {
            $rules['status'] = 'sometimes|required|string|in:pending,approved,rejected';
        }

        return $rules;
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $overtimeRequest = OvertimeRequest::find($this->route('overtimeRequest'));
                $startTime = $this->input('start_time', $overtimeRequest?->start_time);
                $endTime = $this->input('end_time', $overtimeRequest?->end_time);

                if ($startTime && $endTime && $endTime <= $startTime) {
                    $validator->errors()->add('end_time', 'The end time must be after start time.');
                }
            },
        ];
    }
}
