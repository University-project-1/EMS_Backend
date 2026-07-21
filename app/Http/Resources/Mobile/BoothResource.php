<?php

namespace App\Http\Resources\Mobile;

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
            'svg_id' => $this->svg_id,

            'hall_number' => $this->whenLoaded('hall', fn() => $this->hall->number),
            'company' => CompanySummaryResource::make($this->whenLoaded('company')),
            'is_saved' => $this->whenHas(
                'is_saved',
                fn (): bool => (bool) $this->getAttribute('is_saved'),
            ),
        ];
    }
}
