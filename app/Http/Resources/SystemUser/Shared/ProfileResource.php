<?php

namespace App\Http\Resources\SystemUser\Shared;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'email'  => $this->email,
            'type'   => $this->type->value ?? $this->type,
            'avatar' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('avatar'), null),
            'is_verified' => $this->hasVerifiedEmail(),
        ];
    }
}
