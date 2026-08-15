<?php

namespace App\Models;

use App\Enum\Status;
use Database\Factories\VolunteerApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'full_name',
    'email',
    'phone',
    'motivation',
    'education_or_occupation',
    'skills',
    'city',
    'privacy_consent_at',
    'status',
    'reviewed_by',
    'reviewed_at',
    'review_note',
    'whatsapp_notification_sent_at',
    'whatsapp_notification_failed_at',
    'whatsapp_notification_error',
])]
class VolunteerApplication extends Model implements HasMedia
{
    /** @use HasFactory<VolunteerApplicationFactory> */
    use HasFactory, InteractsWithMedia;

    public const CV_COLLECTION = 'cv';

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'privacy_consent_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'whatsapp_notification_sent_at' => 'datetime',
            'whatsapp_notification_failed_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::CV_COLLECTION)
            ->singleFile()
            ->useDisk('local')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === Status::PENDING;
    }
}
