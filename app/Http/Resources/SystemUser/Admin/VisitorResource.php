<?php

namespace App\Http\Resources\SystemUser\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
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
            'phone' => $this->phone,
            'job' => $this->job,
            'location' => $this->location,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'created_at' => $this->created_at,
            'avatar' => $this->getFirstMediaUrl('avatar') ?: null,
        ];
    }
}
