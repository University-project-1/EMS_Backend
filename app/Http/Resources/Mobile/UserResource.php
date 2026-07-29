<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'job' => $this->job,
            'location' => $this->location,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'gender' => $this->gender,
            'phone' => $this->phone,
            'avatar' => $this->getFirstMediaUrl('user-avatars'),
        ];
    }
}
