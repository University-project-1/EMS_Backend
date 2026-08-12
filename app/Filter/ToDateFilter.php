<?php

namespace App\Filter;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ToDateFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->where($property, '<=', CarbonImmutable::parse($value)->endOfDay());
    }
}
