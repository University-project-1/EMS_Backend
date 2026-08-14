<?php

namespace App\Models;

use App\Enum\EventType;
use App\Enum\Status;
use App\Observers\EventObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property Status $status
 * @property EventType $type
 */
#[ObservedBy([EventObserver::class])]
#[Fillable(['eventable_type', 'eventable_id', 'event_hall_id', 'type', 'status', 'qr_token', 'start_at', 'end_at', 'duration', 'title', 'description'])]
class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'duration' => 'integer',
            'status' => Status::class,
            'type' => EventType::class,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('event-logo')->singleFile();
    }

    public function scopeAccessibleBy(Builder $query, SystemUser $systemUser): Builder
    {
        return $query->where(function (Builder $query) use ($systemUser): void {
            $query->where(function (Builder $query) use ($systemUser): void {
                $query->where('eventable_type', SystemUser::class)
                    ->where('eventable_id', $systemUser->getKey());
            })->orWhere(function (Builder $query) use ($systemUser): void {
                $query->where('eventable_type', Company::class)
                    ->whereIn(
                        'eventable_id',
                        $systemUser->companies()->select('companies.id')
                    );
            });
        });
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
