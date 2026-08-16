<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_type_id' => $this->event_type_id,
            'start_time' => $this->start_time?->toIso8601String(),
            'end_time' => $this->end_time?->toIso8601String(),
            'duration_minutes' => $this->duration_minutes,
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'event_type_title' => $this->event_type_title,
        ];
    }
}
