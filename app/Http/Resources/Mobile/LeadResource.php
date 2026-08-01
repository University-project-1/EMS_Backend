<?php

namespace App\Http\Resources\Mobile;

use App\Support\MorphResourceResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'leadable_type' => class_basename($this->leadable_type),
            'leadable_id' => $this->leadable_id,
            'leadable' => MorphResourceResolver::resourceFor($this->leadable),
            'created_at' => $this->created_at,
        ];
    }
}
