<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class BoothSearchFilter implements Filter
{
    public function __invoke(Builder $query,mixed $value,string $property): void {
        $query->whereHas('company', function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%");
        });
    }
}