<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ReviewSearchFilter implements Filter
{
    public function __invoke(Builder $query,mixed $value,string $property): void {
        $query->whereHas('user', function (Builder $query) use ($value) {
            $query->where(function (Builder $query) use ($value) {
                $query->where('first_name', 'like', "%{$value}%")
                    ->orWhere('last_name', 'like', "%{$value}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?",["%{$value}%"]);
            });
        });
    }
}