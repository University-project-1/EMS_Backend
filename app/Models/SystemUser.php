<?php

namespace App\Models;

use App\Enum\SystemUserType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'email', 'password', 'type'])]
#[Hidden(['password'])]
class SystemUser extends Authenticatable implements HasMedia
{
    use HasFactory, InteractsWithMedia, Notifiable, SoftDeletes, HasApiTokens;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'type' => SystemUserType::class,
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_system_users');
    }

    public function booths(): BelongsToMany
    {
        return $this->belongsToMany(Booth::class, 'booth_system_users')->withPivot('assigned_by');
    }

    public function boothRequests(): HasMany
    {
        return $this->hasMany(BoothRequest::class);
    }

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }

    public function deviceTokens(): MorphMany
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');
    }

    public function resolvedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'resolved_by');
    }
}
