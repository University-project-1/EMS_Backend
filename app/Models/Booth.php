<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['hall_id', 'company_id', 'qr_token', 'number', 'svg_id', 'area', 'price'])]
class Booth extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected function casts(): array
    {
        return [
            'area' => 'float',
            'price' => 'decimal:2',
        ];
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function systemUsers(): BelongsToMany
    {
        return $this->belongsToMany(SystemUser::class, 'booth_system_users')->withPivot('assigned_by');
    }

    public function boothRequests(): HasMany
    {
        return $this->hasMany(BoothRequest::class);
    }

    public function leads(): MorphMany
    {
        return $this->morphMany(Lead::class, 'leadable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function savedItems(): MorphMany
    {
        return $this->morphMany(Saved::class, 'savedable');
    }

    public function invitations() : MorphMany{
        return $this->morphMany(Invitation::class, 'inviteable');
    }
}
