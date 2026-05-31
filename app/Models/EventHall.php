<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['number', 'area', 'svg_id', 'price_per_hour'])]
class EventHall extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'area' => 'float',
            'price_per_hour' => 'decimal:2',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
