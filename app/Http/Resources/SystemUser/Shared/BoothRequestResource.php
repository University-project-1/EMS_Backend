<?php

namespace App\Http\Resources\SystemUser\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoothRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $catalog = $this->getFirstMedia('products_catalog');

        return [
            'id' => $this->id,
            'booth_id' => $this->booth_id,
            'company_id' => $this->company_id,
            'company_name' => $this->whenLoaded('company', fn () => $this->company?->name),
            'final_price' => $this->final_price,
            'status' => $this->status?->value ?? $this->status,
            'reason_for_booking' => $this->reason_for_booking,
            'services' => BoothRequestServiceResource::collection($this->whenLoaded('services')),
            'products_count' => $this->whenCounted('products'),
            'products_file_url' => $catalog?->getUrl(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
