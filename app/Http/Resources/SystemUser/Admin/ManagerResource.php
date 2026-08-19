<?php

namespace App\Http\Resources\SystemUser\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManagerResource extends JsonResource
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
            'email' => $this->email,
            'avatar' => $this->getFirstMediaUrl('avatar') ?: null,
            'companies_count' => $this->whenCounted('companies'),
            'booths_count' => $this->when(isset($this->booths_count), $this->booths_count),
            'portfolios' => CompanyDirectoryResource::collection($this->whenLoaded('companies')),
        ];
    }
}
