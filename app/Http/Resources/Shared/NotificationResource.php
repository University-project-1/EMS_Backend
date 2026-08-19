<?php

namespace App\Http\Resources\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $targetId = $this->data['target_id'] ?? null;

        return [
            'id' => $this->id,
            'type' => $this->data['type'] ?? null,
            'title' => __($this->data['title'] ?? ''),
            'body' => __($this->data['body'] ?? ''),
            'target_id' => is_numeric($targetId) ? (int) $targetId : $targetId,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
