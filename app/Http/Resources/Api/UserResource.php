<?php

namespace App\Http\Resources\Api;

use App\Filament\Resources\PositionResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => new RoleResource($this->whenLoaded('role')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'face_embedding_id' => $this->face_embedding_id,
        ];
    }
}
