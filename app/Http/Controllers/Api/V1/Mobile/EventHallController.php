<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Enum\Status;
use App\Filter\BookedBoothFilter;
use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\EventHallResource;
use App\Models\EventHall;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Visitor/EventHall')]
class EventHallController extends Controller
{
    /**
     * all event halls
     */
    #[QueryParameter('filter[number]', 'Filter by hall number', required: false, type: 'string')]
    #[QueryParameter('include', 'Include events relationship', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort by number, area or price_per_hour', required: false, type: 'string')]
    public function index()
    {
        $eventHalls = QueryBuilder::for(EventHall::class)
            ->allowedFilters(AllowedFilter::exact('number'))
            ->allowedIncludes(
                    AllowedInclude::callback('events', fn ($events) => $events
                        ->where('status', Status::APPROVED->value)
                        ->with('media'),
                ),
            )
            ->allowedSorts('number')
            ->get();

        return successResponse(EventHallResource::collection($eventHalls));
    }

    /**
     * show
     */
    public function show(EventHall $eventHall)
    {
        $eventHall->loadMissing([
            'events' => fn ($events) => $events
                ->where('status', Status::APPROVED->value)
                ->with('media'),
        ]);

        return successResponse(EventHallResource::make($eventHall));
    }
}
