<?php

namespace App\Http\Resources\Api;

use App\Filament\Resources\ShiftResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'user' => new UserResource($this->whenLoaded('user')),
            'shift' => new ShiftResource($this->whenLoaded('userShift.shift')),
            'photo' => $this->photo,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'check_in_location' => $this->check_in_location,
            'check_out_location' => $this->check_out_location,
            'status' => $this->status,
        ];
    }
}
