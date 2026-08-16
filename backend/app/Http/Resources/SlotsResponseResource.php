<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotsResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'window' => [
                'start' => $this['window']['start'],
                'end' => $this['window']['end'],
            ],
            'slots' => SlotResource::collection($this['slots']),
        ];
    }
}
