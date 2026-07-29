<?php

namespace App\Http\Resources\SystemUser\Shared;

use App\Http\Resources\Shared\HallResource;
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
            'is_booked' => !is_null($this->qr_token),
            'hall_id' => new HallResource($this->whenLoaded('hall')),
            'company' => $this->whenLoaded('company', function() {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                ];
            }),
            'services' => $this->whenLoaded('boothRequests', function () {
                $services = $this->boothRequests->flatMap->attachedServices;
                return ServiceResource::collection($services);
            }),
            'created_at' => $this->created_at
        ];
    }
}
