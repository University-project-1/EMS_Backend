<?php

namespace App\Http\Resources\SystemUser\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'business_sector' => $this->business_sector,
            'phone' => $this->phone,
            'description' => $this->description,
            'year_founded' => $this->year_founded,
            'social_links' => $this->social_links,
            'headquarters_lat' => (float) $this->headquarters_lat,
            'headquarters_lng' => (float) $this->headquarters_lng,

            'logo' => $this->whenLoaded('logoMedia', fn () => $this->getFirstMediaUrl('logo')),

            'gallery' => $this->whenLoaded('galleryMedia', fn () =>
                $this->getMedia('gallery')->map(fn ($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl()
                ])
            ),
        ];
    }
}
