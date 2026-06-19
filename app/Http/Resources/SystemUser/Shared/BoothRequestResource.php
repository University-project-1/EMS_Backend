<?php

namespace App\Http\Resources\SystemUser\Shared;

use App\Http\Resources\SystemUser\Shared\BoothRequestServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoothRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booth_id' => $this->booth_id,
            'company_id' => $this->company_id,
            'status' => $this->status,
            'reason_for_booking' => $this->reason_for_booking,
            'final_price' => (float) $this->final_price,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            'services' => BoothRequestServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
