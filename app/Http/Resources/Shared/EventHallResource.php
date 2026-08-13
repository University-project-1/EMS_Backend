<?php

namespace App\Http\Resources\Shared;

use App\Enum\SystemUserType;
use App\Models\SystemUser;
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
            'price_per_hour' => $this->when($this->canViewPrice($request), $this->price_per_hour),
            'events' => EventResource::collection($this->whenLoaded('events')),
        ];
    }

    private function canViewPrice(Request $request){
        $systemUser = $request->user('system');

        return $systemUser instanceof SystemUser
            && ($systemUser->type === SystemUserType::ADMIN || $systemUser->type === SystemUserType::EXHIBITOR);

    }
}
