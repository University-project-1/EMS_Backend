<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\DTOs\SystemUser\CompanyDTO;
use App\DTOs\SystemUser\EventDTO;
use App\Enum\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Exhibitor\EventCalendarRequest;
use App\Http\Requests\SystemUser\Exhibitor\StoreEventRequest;
use App\Http\Resources\Shared\EventResource;
use App\Models\Event;
use App\Services\SystemUser\Exhibitor\EventService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Exhibitor/Events')]
class EventController extends Controller
{
    public function __construct(protected EventService $eventService) {}

    /**
     * statistics
     */
    public function statistics()
    {
        $statistics = Event::query()
            ->accessibleBy(auth('system')->user())
            ->selectRaw('COUNT(*) as total_requests')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_requests', [Status::PENDING->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved_requests', [Status::APPROVED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected_requests', [Status::REJECTED->value])
            ->firstOrFail();

        return successResponse([
            'total_requests' => (int) $statistics->getAttribute('total_requests'),
            'pending_requests' => (int) $statistics->getAttribute('pending_requests'),
            'approved_requests' => (int) $statistics->getAttribute('approved_requests'),
            'rejected_requests' => (int) $statistics->getAttribute('rejected_requests'),
        ]);
    }

    /**
     * myEvents
     */
    #[QueryParameter('filter[status]', 'Filter booths by exact booth status', required: false, type: 'string')]
    public function index()
    {
        $events = QueryBuilder::for(Event::class)
            ->allowedFilters(
                AllowedFilter::exact('status')
            )
            ->accessibleBy(auth('system')->user())
            ->select('events.*')
            ->selectRaw('1 as can_view_qr')
            ->with(['media', 'speakers', 'eventable'])
            ->withAvg('reviews', 'rating')
            ->withCount(['leads', 'savedItems', 'reviews'])
            ->latest('created_at')
            ->paginate(10);

        return successResponse(EventResource::collection($events));
    }

    /**
     * booked appointments
     */
    public function calendar(EventCalendarRequest $request)
    {
        $validated = $request->validated();
        $from = Carbon::parse($validated['from']);
        $to = Carbon::parse($validated['to']);

        $bookedSlots = Event::query()
            ->where('event_hall_id', $validated['event_hall_id'])
            ->where('status', Status::APPROVED)
            ->where('start_at', '<', $to)
            ->where('end_at', '>', $from)
            ->orderBy('start_at')
            ->get(['id', 'start_at', 'end_at']);

        return successResponse($bookedSlots);
    }

    /**
     * Nearest upcoming events.
     */
    public function nearest()
    {
        $events = Event::query()
            ->where('status', Status::APPROVED->value)
            ->where('start_at', '>=', now())
            ->with(['speakers', 'eventable', 'media'])
            ->withAvg('reviews', 'rating')
            ->withCount(['leads', 'savedItems'])
            ->orderBy('start_at')
            ->orderBy('id')
            ->limit(10)
            ->get();

        return successResponse(EventResource::collection($events));
    }

    /**
     * booking event hall
     */
    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        $eventDto = EventDTO::fromRequest($validated);

        $companyDto = isset($validated['new_company'])
            ? CompanyDTO::fromRequest($validated['new_company'])
            : null;

        $event = $this->eventService->store(
            auth('system')->user(),
            $eventDto,
            $companyDto
        );

        return successResponse(EventResource::make($event));
    }
}
