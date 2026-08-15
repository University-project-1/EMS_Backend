<?php

// app/Http/Resources/SystemUser/Admin/VolunteerApplicationResource.php

namespace App\Http\Resources\SystemUser\Admin;

use App\Models\VolunteerApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin VolunteerApplication */
class VolunteerApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'created_at' => $this->created_at,

            $this->mergeWhen($this->detailsWereLoaded(), [
                'motivation' => $this->motivation,
                'education_or_occupation' => $this->education_or_occupation,
                'skills' => $this->skills,
                'city' => $this->city,
                'privacy_consent_at' => $this->privacy_consent_at,
                'cv' => $this->whenLoaded('media', function (): ?array {
                    $cv = $this->getFirstMedia(VolunteerApplication::CV_COLLECTION);

                    return $cv ? [
                        'id' => $cv->getKey(),
                        'name' => $cv->name,
                        'mime_type' => $cv->mime_type,
                        'size' => $cv->size,
                        'url' => route('admin.volunteer-applications.cv', $this->resource),
                    ] : null;
                }),
                'reviewed_at' => $this->reviewed_at,
                'review_note' => $this->review_note,
                'reviewer' => $this->whenLoaded('reviewer', fn (): ?array => $this->reviewer ? [
                    'id' => $this->reviewer->id,
                    'name' => $this->reviewer->name,
                    'email' => $this->reviewer->email,
                ] : null),
                'whatsapp_notification' => [
                    'sent_at' => $this->whatsapp_notification_sent_at,
                    'failed_at' => $this->whatsapp_notification_failed_at,
                ],
                'updated_at' => $this->updated_at,
            ]),
        ];
    }

    private function detailsWereLoaded(): bool
    {
        return $this->resource->relationLoaded('media');
    }
}
