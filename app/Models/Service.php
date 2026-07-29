<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'price', 'is_active'])]
class Service extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function boothRequestServices(): HasMany
    {
        return $this->hasMany(BoothRequestService::class, 'service_id');
    }
}
