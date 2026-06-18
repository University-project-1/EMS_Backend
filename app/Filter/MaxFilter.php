<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class MaxFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->where($property, '<=', $value);
    }
}
