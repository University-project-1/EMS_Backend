<?php

namespace App\Filter;

use App\Models\Booth;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ReportSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->whereHasMorph(
            'reportable',
            [Event::class, Booth::class],
            function (Builder $query, string $type) use ($value) {
                if ($type === Event::class) {
                    $query->where('title', 'like', "%{$value}%");
                }
                if ($type === Booth::class) {
                    $query->where('number', 'like', "%{$value}%");
                }
            }
        );
    }
}