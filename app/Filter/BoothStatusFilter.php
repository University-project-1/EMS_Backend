<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class BoothStatusFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->whereHas('latestBoothRequest', function (Builder $query) use ($value) {
            $query->where('status', $value);
        });
    }
}