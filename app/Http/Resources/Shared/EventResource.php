<?php

namespace App\Http\Resources\Shared;

use App\Enum\SystemUserType;
use App\Http\Resources\SystemUser\Shared\CompanyResource;
use App\Http\Resources\SystemUser\Shared\SystemUserResource;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
class EventResource extends JsonResource
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
            'title' => $this->title,
            'event_hall_id' => $this->event_hall_id,
            'type' => $this->type,
            'status' => $this->status,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'duration' => $this->duration,
            'description' => $this->description,
            'qr_token' => $this->when($this->canViewQr($request), $this->qr_token),
            'eventable' => $this->eventableResource(),
            'speakers' => SpeakerResource::collection($this->whenLoaded('speakers')),
            'average_rating' => $this->whenAggregated(
                'reviews',
                'rating',
                'avg',
                fn (mixed $average): float => round((float) $average, 2),
            ),
            'qr_scans_count' => $this->whenCounted('leads'),
            'saved_count' => $this->whenCounted('savedItems'),
            'is_saved' => $this->whenHas(
                'is_saved',
                fn (): bool => (bool) $this->getAttribute('is_saved'),
            ),
            'created_at' => $this->created_at,
            'logo' => $this->whenLoaded(
                'media',
                fn (): ?string => $this->getFirstMediaUrl('event-logo') ?: null,
            ),
        ];
    }

    private function canViewQr(Request $request): bool
    {
        $systemUser = $request->user('system');

        return $systemUser instanceof SystemUser
            && ($systemUser->type === SystemUserType::ADMIN || (bool) $this->getAttribute('can_view_qr'));
    }

    private function eventableResource(): mixed
    {
        if ($this->eventable_type === Company::class) {
            return $this->whenLoaded('eventable', fn () => CompanyResource::make($this->eventable));
        }

        if ($this->eventable_type === SystemUser::class) {
            return $this->whenLoaded('eventable', fn () => SystemUserResource::make($this->eventable));
        }

        return null;
    }
}
