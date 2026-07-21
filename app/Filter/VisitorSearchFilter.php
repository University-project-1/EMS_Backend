<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class VisitorSearchFilter implements Filter
{
    public function __invoke(Builder $query,mixed $value,string $property): void {
        $query->where(function ($q) use ($value) {
            $q->where('first_name', 'like', "%{$value}%")
                ->orWhere('last_name', 'like', "%{$value}%")
                ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?",["%{$value}%"])
                ->orWhere('email', 'like', "%{$value}%")
                ->orWhere('phone', 'like', "%{$value}%");
        });
    }
}