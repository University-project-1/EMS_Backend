<?php
namespace App\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class CompanyNameFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('company', function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%");
        });
    }
}
