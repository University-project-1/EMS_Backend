<?php

namespace App\Http\Resources\SystemUser\Exhibitor;

use App\Http\Resources\SystemUser\Shared\SystemUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitaionResource extends JsonResource
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
            'email' => $this->email,
            'status' => $this->status,
            'token' => $this->when($this->status === 'pending', $this->token),
            'expires_at' => $this->expires_at,
            'is_expired' => $this->expires_at < now() && $this->status === 'pending',

            'sender' => new SystemUserResource($this->whenLoaded('sender')),

            'type' => strtolower(class_basename($this->inviteable_type)),
            'name' => $this->inviteable->name ?? $this->inviteable->booth_number ?? 'Unknown',

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
