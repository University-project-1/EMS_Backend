<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class BoothStatusFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $userId = request()->user('system')?->getKey();

        $query->whereHas('boothRequests', function (Builder $requestQuery) use ($value, $userId): void {
            $requestQuery
                ->where('status', (string) $value)
                ->when($userId, function (Builder $query) use ($userId): void {
                    $query->where('system_user_id', $userId);
                });
        });
    }
}
