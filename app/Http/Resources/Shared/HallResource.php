<?php

namespace App\Http\Resources\Shared;

use App\Http\Resources\SystemUser\Shared\BoothResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
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
            'area' => $this->area,
            'type' => $this->type,
            'svg_id' => $this->svg_id,

            'booths' => BoothResource::collection($this->whenLoaded('booths')),
        ];
    }
}
