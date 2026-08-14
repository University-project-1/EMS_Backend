<?php

namespace App\Http\Resources\SystemUser\Exhibitor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LookupResource extends JsonResource
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
            'label' => match (true) {
                isset($this->number) => "Booth {$this->number}" . ($this->company ? " ({$this->company->name})" : ''),
                isset($this->title) => $this->title,
                default => $this->name,
            },
        ];
    }
}
