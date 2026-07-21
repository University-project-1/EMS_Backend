<?php
namespace App\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class BookedBoothFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $isBooked = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($isBooked) {
            $query->whereNotNull('company_id');
        } else {
            $query->whereNull('company_id');
        }
    }
}
