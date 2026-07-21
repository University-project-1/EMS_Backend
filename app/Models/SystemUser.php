<?php

namespace App\Models;

use App\Enum\SystemUserType;
use App\Notifications\Auth\ResetApiPassword;
use App\Notifications\Auth\VerifyApiEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
use Override;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/** @property SystemUserType $type */
#[Fillable(['name', 'email', 'password', 'type', 'google_id', 'email_verified_at'])]
#[Hidden(['password'])]
class SystemUser extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use CanResetPassword, HasApiTokens, HasFactory, InteractsWithMedia, Notifiable,  SoftDeletes;

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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyApiEmail);
    }

    #[Override]
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetApiPassword($token));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
