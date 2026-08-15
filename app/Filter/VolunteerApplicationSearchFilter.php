<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class VolunteerApplicationSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $search = trim((string) $value);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('full_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
