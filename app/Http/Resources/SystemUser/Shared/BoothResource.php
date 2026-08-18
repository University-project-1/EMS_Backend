<?php

namespace App\Http\Resources\SystemUser\Shared;

use App\Enum\Status;
use App\Http\Resources\Shared\HallResource;
use BackedEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoothResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $approvedRequests = $this->relationLoaded('boothRequests')
            ? $this->boothRequests
            : null;
        $approvedRequest = $approvedRequests?->first();
        $latestRequest = $this->relationLoaded('latestBoothRequest')
            ? $this->latestBoothRequest
            : null;
        $requestForDetails = $approvedRequest ?? $latestRequest;
        $company = $this->relationLoaded('company') && $this->company
            ? $this->company
            : $requestForDetails?->company;
        $requestStatus = $requestForDetails?->status;
        $status = $requestStatus instanceof BackedEnum
            ? $requestStatus->value
            : ($requestStatus ?? Status::PENDING->value);

        if ($approvedRequests !== null) {
            $services = $approvedRequests->flatMap(
                fn ($boothRequest) => $boothRequest->relationLoaded('services')
                    ? $boothRequest->services
                    : collect(),
            )->values();
            $servicesResource = BoothRequestServiceResource::collection($services);
        } else {
            $servicesResource = ServiceResource::collection(
                $latestRequest?->attachedServices ?? collect(),
            );
        }

        return [
            'id' => $this->id,
            'number' => $this->number,
            'qr_token' => $this->qr_token,
            'qr_code_url' => $this->getFirstMediaUrl('qr_code'),
            'area' => $this->area,
            'price' => $this->price,
            'svg_id' => $this->svg_id,
            'is_booked' => ! is_null($this->qr_token),
            'status' => $status,
            'is_saved' => $this->whenHas(
                'is_saved',
                fn (): bool => (bool) $this->getAttribute('is_saved'),
            ),
            'is_review' => $this->whenHas(
                'is_review',
                fn (): bool => (bool) $this->getAttribute('is_review'),
            ),
            'hall_id' => new HallResource($this->whenLoaded('hall')),
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'status' => $company->status instanceof BackedEnum
                    ? $company->status->value
                    : ($company->status ?? null),
            ] : null,
            'services' => $servicesResource,
            'created_at' => $this->created_at,
        ];
    }
}
