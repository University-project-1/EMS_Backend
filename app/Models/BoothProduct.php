<?php

namespace App\Models;

use Database\Factories\BoothProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['booth_request_id', 'name', 'price', 'description', 'sort_order'])]
class BoothProduct extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function boothRequest(): BelongsTo
    {
        return $this->belongsTo(BoothRequest::class);
    }
}
