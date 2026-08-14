<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;
use Carbon\CarbonImmutable;

class CreatedToFilter implements Filter
{
    public function __invoke(Builder $query,mixed $value,string $property): void {
    $query->where($property, '<=',CarbonImmutable::parse($value)->endOfDay());
    }
}
