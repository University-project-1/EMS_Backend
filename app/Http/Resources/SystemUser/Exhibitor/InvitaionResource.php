<?php

namespace App\Http\Resources\SystemUser\Exhibitor;

use App\Http\Resources\SystemUser\Shared\SystemUserResource;
use App\Models\SystemUser;
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
            'is_user_exists' => SystemUser::where('email', $this->email)->whereNotNull('email_verified_at' )->exists(),
            'is_logged_in' => auth('system_user')->check() && auth('system_user')->user()->email === $this->email,
            'expires_at' => $this->expires_at,
            'is_expired' => $this->expires_at < now() && $this->status === 'pending',

            'sender' => new SystemUserResource($this->whenLoaded('sender')),

            'type' => strtolower(class_basename($this->inviteable_type)),
            'name' => $this->inviteable->name ?? $this->inviteable->booth_number ?? 'Unknown',

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
