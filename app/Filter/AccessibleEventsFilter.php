<?php

namespace App\Filters;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AccessibleEventsFilter implements Filter
{
    public function __construct(protected SystemUser $user) {}

    public function __invoke(Builder $query, $value, string $property): void
    {
        $user = $this->user;

        $query->where(function (Builder $q) use ($user) {
            $q->where(function (Builder $sub) use ($user) {
                $sub->where('eventable_type', SystemUser::class)
                    ->where('eventable_id', $user->getKey());
            })->orWhere(function (Builder $sub) use ($user) {
                $sub->where('eventable_type', Company::class)
                    ->whereIn(
                        'eventable_id',
                        $user->companies()->select('companies.id')
                    );
            });
        });
    }
}
