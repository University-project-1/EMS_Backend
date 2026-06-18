<?php

namespace App\Http\Resources\SystemUser\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoothResource extends JsonResource
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
            'qr_token' => $this->qr_token,
            'area' => $this->area,
            'price' => $this->price,
            'svg_id' => $this->svg_id,

            'hall_id' => $this->whenLoaded('hall', $this->hall_id),
            'company_id' => $this->whenLoaded('company', $this->company_id),
        ];
    }
}
