<?php

namespace App\Filter;

use App\Models\SystemUser;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AccessibleBoothsFilter implements Filter
{
    public function __construct(protected SystemUser $user) {}

    public function __invoke(Builder $query, $value, string $property): void
    {
        $user = $this->user;

        $query->accessibleBy($user);
    }
}
