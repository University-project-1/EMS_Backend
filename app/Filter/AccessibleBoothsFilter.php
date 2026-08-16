<?php

namespace App\Filter;

use App\Models\SystemUser;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AccessibleBoothsFilter implements Filter
{
    public function __construct(
        private readonly SystemUser $user,
    ) {}

    public function apply(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereHas('systemUsers', function (Builder $sub): void {
                $sub->where('system_users.id', $this->user->getKey());
            })->orWhereIn('company_id', function ($sub): void {
                $sub->select('company_id')
                    ->from('company_system_users')
                    ->where('system_user_id', $this->user->getKey());
            });
        });
    }

    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $this->apply($query);
    }
}
