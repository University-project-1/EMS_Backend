<?php

namespace App\Services\Mobile;

use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use App\Models\Lead;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LeadInterestAudienceResolver
{
    public function forCompany(Company $company): Collection
    {
        $boothIds = Booth::query()
            ->where('company_id', $company->getKey())
            ->select('id');

        $eventIds = Event::query()
            ->where('eventable_type', Company::class)
            ->where('eventable_id', $company->getKey())
            ->select('id');

        $leadUserIds = Lead::query()
            ->where(function (Builder $query) use ($boothIds, $eventIds): void {
                $query
                    ->where(function (Builder $query) use ($boothIds): void {
                        $query
                            ->where('leadable_type', Booth::class)
                            ->whereIn('leadable_id', $boothIds);
                    })
                    ->orWhere(function (Builder $query) use ($eventIds): void {
                        $query
                            ->where('leadable_type', Event::class)
                            ->whereIn('leadable_id', $eventIds);
                    });
            })
            ->select('user_id');

        return User::query()
            ->whereIn('id', $leadUserIds)
            ->get();
    }

    public function forOrganizer(SystemUser $organizer): Collection
    {
        $eventIds = Event::query()
            ->where('eventable_type', SystemUser::class)
            ->where('eventable_id', $organizer->getKey())
            ->select('id');

        $leadUserIds = Lead::query()
            ->where('leadable_type', Event::class)
            ->whereIn('leadable_id', $eventIds)
            ->select('user_id');

        return User::query()
            ->whereIn('id', $leadUserIds)
            ->get();
    }
}
