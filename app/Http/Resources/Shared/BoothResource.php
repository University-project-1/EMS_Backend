<?php

namespace App\Http\Resources\Shared;

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
            'svg_is' => $this->svg_is,

            'company_id' => $this->company_id,
            'hall_id' => $this->hall_id,
        ];
    }
}
