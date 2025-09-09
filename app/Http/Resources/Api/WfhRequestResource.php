<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WfhRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'tanggal' => $this->tanggal,
            'alasan' => $this->reason,
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'admin_notes' => $this->admin_notes,
            'notes_by' => $this->notes_by,
        ];
    }
}
