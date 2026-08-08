<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Filter\AccessibleBoothsFilter;
use App\Filter\AccessibleCompaniesFilter;
use App\Filter\LookupSearchFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Exhibitor\LookupResource;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Exhibitor/Lookup')]
class LookupController extends Controller
{
    /**
     * companies
    */
    #[QueryParameter('filter[search]', 'Search by company name (partial match)', required: false, type: 'string')]
    public function companies(): JsonResponse
    {
        $user = Auth::guard('system')->user();

        $companies = Cache::remember("lookup_companies_user_{$user->id}", now()->addMinutes(15), function () use ($user) {
            $baseQuery = Company::query();
            (new AccessibleCompaniesFilter($user))($baseQuery, null, '');

            $models = QueryBuilder::for($baseQuery)
                ->select(['id', 'name'])
                ->allowedFilters(AllowedFilter::custom('search', new LookupSearchFilter))
                ->defaultSort('name')
                ->get();
            return LookupResource::collection($models)->resolve();
        });

        return successResponse($companies);
    }

    /**
     * booths
     */
    #[QueryParameter('filter[search]', 'Search by booth number (partial match)', required: false, type: 'string')]
    #[QueryParameter('filter[hall_id]', 'Filter booths by specific hall ID', required: false, type: 'integer')]
    public function booths(): JsonResponse
    {
        $user = Auth::guard('system')->user();

        $booths = Cache::remember("lookup_booths_user_{$user->id}", now()->addMinutes(15), function () use ($user) {
            $baseQuery = Booth::query();
            (new AccessibleBoothsFilter($user))($baseQuery, null, '');

            $models = QueryBuilder::for($baseQuery)
                ->select(['id', 'number'])
                ->with(['company:id,name'])
                ->allowedFilters(
                    AllowedFilter::custom('search', new LookupSearchFilter),
                    AllowedFilter::exact('hall_id'),
                )
                ->defaultSort('number')
                ->get();

            return LookupResource::collection($models)->resolve();
        });
        return successResponse($booths);
    }
    /**
     * events
     */
    #[QueryParameter('filter[search]', 'Search by event title (partial match)', required: false, type: 'string')]
    #[QueryParameter('filter[type]', 'Filter events by type (exact match)', required: false, type: 'string')]
    public function events(): JsonResponse
    {
        $user = Auth::guard('system')->user();

        $events = Cache::remember("lookup_events_user_{$user->id}", now()->addMinutes(15), function () use ($user) {
            $baseQuery = Event::query()->accessibleBy($user);

            $models = QueryBuilder::for($baseQuery)
                ->select(['id', 'title'])
                ->allowedFilters(
                    AllowedFilter::custom('search', new LookupSearchFilter),
                    AllowedFilter::exact('type'),
                )
                ->defaultSort('title')
                ->get();

            return LookupResource::collection($models)->resolve();
        });
        return successResponse($events);
    }
}
