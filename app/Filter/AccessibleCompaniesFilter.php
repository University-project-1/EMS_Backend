<?php

namespace App\Filter;

use App\Models\SystemUser;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AccessibleCompaniesFilter implements Filter
{
    public function __construct(protected SystemUser $user) {}

    public function __invoke(Builder $query, $value, string $property): void
    {
        $user = $this->user;

        $query->whereHas('systemUsers', function (Builder $q) use ($user) {
            $q->where('system_users.id', $user->getKey());
        });
    }
}
