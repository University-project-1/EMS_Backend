<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class BoothProductsSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        if (! is_string($value)) {
            return;
        }

        $search = trim($value);

        if ($search === '') {
            return;
        }

        if (DB::getDriverName() === 'mysql' && mb_strlen($search) >= 3) {
            $query->whereFullText(['name', 'description'], $search);

            return;
        }

        $query->where(function (Builder $query) use ($search): void {
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
        });
    }
}
