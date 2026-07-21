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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
    public function index()
    {
        $events = Event::query()->accessibleBy(auth('system')->user())
            ->select('events.*')
            ->selectRaw('1 as can_view_qr')
            ->with(['media', 'speakers', 'eventable'])
            ->withAvg('reviews', 'rating')
            ->withCount(['leads', 'savedItems'])
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
