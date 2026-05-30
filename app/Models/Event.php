<?php

namespace App\Models;

use App\Enum\EventType;
use App\Enum\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['eventable_type', 'eventable_id', 'event_hall_id', 'type', 'status', 'qr_token', 'date', 'duration', 'title', 'description'])]
class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'duration' => 'integer',
            'status' => Status::class,
            'type' => EventType::class,
        ];
    }

    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    public function eventHall(): BelongsTo
    {
        return $this->belongsTo(EventHall::class);
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class);
    }

    public function leads(): MorphMany
    {
        return $this->morphMany(Lead::class, 'leadable');
    }

    public function savedItems(): MorphMany
    {
        return $this->morphMany(Saved::class, 'savedable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
