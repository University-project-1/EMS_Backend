<?php

namespace App\Models;

use App\Enum\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['booth_request_id', 'name', 'price', 'description', 'sort_order'])]
class BoothProduct extends Model
{
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

    /**
     * @param  Builder<BoothProduct>  $query
     * @return Builder<BoothProduct>
     */
    public function scopeForApprovedBooths(Builder $query): Builder
    {
        return $query->whereHas(
            'boothRequest',
            fn (Builder $query): Builder => $query->where('status', Status::APPROVED),
        );
    }
}
