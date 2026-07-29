<?php

namespace App\Http\Resources\SystemUser\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'is_active' => $this->is_active,

            'quantity' => $this->whenPivotLoaded('booth_request_services', fn () => $this->pivot->quantity),
            'unit_price' => $this->whenPivotLoaded('booth_request_services', fn () => (float) $this->pivot->unit_price),
            'total_price' => $this->whenPivotLoaded('booth_request_services', function () {
                return (float) ($this->pivot->quantity * $this->pivot->unit_price);
            }),
        ];
    }
}
