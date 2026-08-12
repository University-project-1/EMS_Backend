<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventHallResource extends JsonResource
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
            'number' => $this->number,
            'svg_id' => $this->svg_id,
            'area' => $this->area,
            'price_per_hour' => $this->price_per_hour,
            'events' => EventResource::collection($this->whenLoaded('events')),
        ];
    }
}
