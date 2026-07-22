<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;
use Carbon\CarbonImmutable;

class CreatedFromFilter implements Filter
{
    public function __invoke(Builder $query,mixed $value,string $property): void {
        $query->where('created_at','>=',CarbonImmutable::parse($value)->startOfDay());
    }
}