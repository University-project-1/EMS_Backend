<?php

namespace App\Http\Resources\SystemUser\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDirectoryResource extends JsonResource
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
            'status' => $this->status,
            'logo' => $this->getFirstMediaUrl('logo') ?: null,
            'managers_count' => $this->whenCounted('systemUsers'),
            'booths_count' => $this->whenCounted('booths'),
            'managers' => ManagerResource::collection($this->whenLoaded('systemUsers')),
            'booths' => $this->whenLoaded('booths', function () {
                return $this->booths->map(fn ($booth) => [
                    'id' => $booth->id,
                    'number' => $booth->number,
                    'hall' => $booth->hall?->number,
                    'label' => $booth->hall ? "Booth {$booth->hall->number}-{$booth->number}" : "Booth {$booth->number}",
                ]);
            }),
        ];
    }
}
