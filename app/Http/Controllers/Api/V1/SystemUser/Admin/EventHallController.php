<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\UpdateEventHallDTO;
use App\Enum\Status;
use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\UpdateEventHallRequest;
use App\Http\Resources\Shared\EventHallResource;
use App\Models\EventHall;
use App\Services\SystemUser\Admin\EventHallService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/EventHall')]
class EventHallController extends Controller
{
    public function __construct(protected EventHallService $eventHallService){}

        /**
     * all event halls
     */
    #[QueryParameter('filter[number]', 'Filter by hall number', required: false, type: 'string')]
    #[QueryParameter('filter[min_area]', 'Minimum area', required: false, type: 'number')]
    #[QueryParameter('filter[max_area]', 'Maximum area', required: false, type: 'number')]
    #[QueryParameter('filter[min_price]', 'Minimum price per hour', required: false, type: 'number')]
    #[QueryParameter('filter[max_price]', 'Maximum price per hour', required: false, type: 'number')]
    #[QueryParameter('include', 'Include events relationship', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort by number, area or price_per_hour', required: false, type: 'string')]
    public function index()
    {
        $eventHalls = QueryBuilder::for(EventHall::class)
            ->allowedFilters(
                AllowedFilter::exact('number'),
                AllowedFilter::custom('min_area', new MinFilter, 'area'),
                AllowedFilter::custom('max_area', new MaxFilter, 'area'),
                AllowedFilter::custom('min_price', new MinFilter, 'price_per_hour'),
                AllowedFilter::custom('max_price', new MaxFilter, 'price_per_hour'),
            )
            ->allowedIncludes(
                    AllowedInclude::callback('events', fn ($events) => $events
                        ->where('status', Status::APPROVED->value)
                        ->with('media'),
                ),
            )
            ->allowedSorts('number', 'area','price_per_hour')
            ->get();

        return successResponse(EventHallResource::collection($eventHalls));
    }

    /**
     * show
     */
    public function show(EventHall $eventHall)
    {
        $eventHall->load([
            'events' => fn ($events) => $events
                ->where('status', Status::APPROVED->value)
                ->with('media'),
        ]);

        return successResponse(EventHallResource::make($eventHall));
    }

    /**
     * update
     */
    public function update(EventHall $eventHall,UpdateEventHallRequest $request){
        $updatedEventHall = $this->eventHallService->update(
            $eventHall,
            UpdateEventHallDTO::fromRequest($request->validated())
        );

        return successResponse(EventHallResource::make($updatedEventHall));
    }
}
