<?php

namespace App\Filter;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class EventDateFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $startOfDay = CarbonImmutable::parse((string) $value)->startOfDay();
        $startOfNextDay = $startOfDay->addDay();

        $query
            ->where('start_at', '<', $startOfNextDay)
            ->where('end_at', '>', $startOfDay);
    }
}
