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

        $query->where(function (Builder $q) use ($user) {
            $q->whereHas('systemUsers', function (Builder $sub) use ($user) {
                $sub->where('system_users.id', $user->getKey());
            })->orWhereIn('company_id', function ($sub) use ($user) {
                $sub->select('company_id')
                    ->from('company_system_users')
                    ->where('system_user_id', $user->getKey());
            });
        });
    }
}
