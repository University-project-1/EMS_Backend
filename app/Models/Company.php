<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'business_sector', 'social_links', 'phone', 'year_founded', 'description', 'headquarters_lat', 'headquarters_lng'])]
class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'year_founded' => 'integer',
            'headquarters_lat' => 'float',
            'headquarters_lng' => 'float',
        ];
    }

    public function systemUsers(): BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'company_system_users');
    }

    public function booths(): HasMany
    {
        return $this->hasMany(Booth::class);
    }

    public function boothRequests(): HasMany
    {
        return $this->hasMany(BoothRequest::class);
    }

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }

    public function savedItems(): MorphMany
    {
        return $this->morphMany(Saved::class, 'savedable');
    }
}
