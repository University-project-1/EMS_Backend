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
        $latestRequest = $this->relationLoaded('latestBoothRequest')
            ? $this->latestBoothRequest
            : null;

        $company = $this->relationLoaded('company') && $this->company
            ? $this->company
            : $latestRequest?->company;

        $requestStatus = $latestRequest?->status;
        $status = $requestStatus instanceof BackedEnum
            ? $requestStatus->value
            : ($requestStatus ?? Status::PENDING->value);

        $services = $latestRequest?->attachedServices ?? collect();

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
            'hall_id' => new HallResource($this->whenLoaded('hall')),
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'status' => $company->status instanceof BackedEnum
                    ? $company->status->value
                    : ($company->status ?? null),
            ] : null,
            'services' => ServiceResource::collection($services),
            'created_at' => $this->created_at,
        ];
    }
}
