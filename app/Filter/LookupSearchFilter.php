<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class LookupSearchFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        if (is_string($value) && trim($value) !== '') {
            $query->where(function (Builder $q) use ($value) {
                $q->where('name', 'like', "%{$value}%")
                  ->orWhere('number', 'like', "%{$value}%")
                  ->orWhere('title', 'like', "%{$value}%");
            });
        }
    }
}
