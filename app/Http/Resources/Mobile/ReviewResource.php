<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user', function(){
                return [
                    'name' => $this->user->first_name . ' ' . $this->user->last_name,
                    'avatar' => $this->user->getFirstMediaUrl('user-avatars'),
                ];
            }),

        ];
    }
}
