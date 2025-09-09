<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'name'  => $this->name,
            'deskripsi' => $this->deskripsi,
            'deadline' => $this->deadline,
            'created_by' => $this->created_by,
        ];
    }
}
