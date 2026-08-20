<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Enum\Status;
use App\Filter\DateFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\ApproveEventRequest;
use App\Http\Resources\Shared\EventResource;
use App\Models\Event;
use App\Services\SystemUser\Admin\EventRequestService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/EventRequests')]
class EventRequestController extends Controller
{
    public function __construct(protected EventRequestService $eventRequestService) {}

    /**
     * statistics
     */
    public function statistics()
    {
        $statistics = Event::query()
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

    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15')]
    #[QueryParameter('filter[title]', type: 'string', description: 'Filter by request name (partial match).')]
    #[QueryParameter('filter[status]', type: 'string', description: 'Filter by request status (exact match).')]
    #[QueryParameter('filter[created_date]', type: 'string', description: 'Filter by created date (mapped to created_at).')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by created_at. Use -created_at for descending.')]
    #[QueryParameter('include', 'Include related resources (eventable, speakers)', required: false, type: 'string')]
    /**
     * all requests
     */
    public function index()
    {
        $perPage = min(max(request()->integer('per_page', 15), 1), 100);

        $eventRequests = QueryBuilder::for(Event::class)
            ->allowedFilters(
                AllowedFilter::partial('title'),
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_date', new DateFilter, 'created_at'),
            )
            ->allowedSorts('created_at')
            ->allowedIncludes('eventable', 'speakers')
            ->defaultSort('-created_at')
            ->with('media')
            ->paginate($perPage);

        return successResponse(EventResource::collection($eventRequests));
    }

    /**
     * show request
     */
    public function show(Event $event)
    {
        $event->load('media', 'speakers', 'eventable', 'eventHall')
            ->loadAvg('reviews', 'rating')
            ->loadCount('leads', 'savedItems');

        return successResponse(EventResource::make($event));
    }

    /**
     * approve request
     */
    public function approve(ApproveEventRequest $request, Event $event)
    {
        $force = $request->boolean('force', false);
        if (! $force) {
            $conflicts = $this->eventRequestService->getConflictingRequests($event);
            if ($conflicts->isNotEmpty()) {
                return errorResponse(
                    errors: [
                        'data' => EventResource::collection($conflicts)->response()->getData(true),
                    ],
                    code: 409,
                );
            }
        }
        $this->eventRequestService->approve($event);

        return successResponse();
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Event $event)
    {
        $this->eventRequestService->sendPaymentReminder($event);

        return successResponse(
            data: null,
            message: 'payment reminder sent successfully',
        );
    }

    /**
     * reject request
     */
    public function reject(Event $event)
    {
        $this->eventRequestService->reject($event);

        return successResponse();
    }
}
