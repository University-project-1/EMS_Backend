<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Enum\Status;
use App\Filter\DateFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\ApproveEventRequest;
use App\Http\Resources\SystemUser\Shared\BoothRequestResource;
use App\Models\BoothRequest;
use App\Services\SystemUser\Admin\BoothRequestService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/BoothRequests')]
class BoothRequestController extends Controller
{
    public function __construct(
        public readonly BoothRequestService $boothRequestService,
    ) {}

    /**
     * statistics
     */
    public function statistics()
    {

        $stats = BoothRequest::query()
            ->selectRaw('COUNT(*) AS total_requests')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pending_requests', [Status::PENDING->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS approved_requests', [Status::APPROVED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS rejected_requests', [Status::REJECTED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS canceled_requests', [Status::CANCELED->value])
            ->first();

        $result = [
            'total_requests' => (int) $stats->total_requests,
            'pending_requests' => (int) $stats->pending_requests,
            'approved_requests' => (int) $stats->approved_requests,
            'rejected_requests' => (int) $stats->rejected_requests,
            'canceled_requests' => (int) $stats->canceled_requests,
        ];

        return successResponse(
            data: $result,
            message: 'booth request statistics retrived successfully',
        );
    }

    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15')]
    #[QueryParameter('filter[name]', type: 'string', description: 'Filter by request name (partial match).')]
    #[QueryParameter('filter[status]', type: 'string', description: 'Filter by request status (exact match).')]
    #[QueryParameter('filter[created_date]', type: 'string', description: 'Filter by created date (mapped to created_at).')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by created_at. Use -created_at for descending.')]
    #[QueryParameter('include', type: 'string', description: 'Include related resources. Allowed: company.')]
    /**
     * all requests
     */
    public function index()
    {
        $boothRequests = QueryBuilder::for(BoothRequest::class)
            ->with('company:id,name')
            ->allowedFilters('company.name',
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_date', new DateFilter, 'created_at'),
            )
            ->allowedSorts('created_at')
            ->paginate(request()->query('per_page', 15));

        return successResponse(
            data: BoothRequestResource::collection($boothRequests),
            message: 'booth requests retrived successfully',
        );
    }

    /**
     * show request
     */
    public function show(BoothRequest $boothRequest)
    {
        $boothRequest->load(['systemUser', 'company', 'company.logoMedia', 'company.galleryMedia', 'booth', 'services']);

        return successResponse(
            data: new BoothRequestResource($boothRequest),
            message: 'booth request retrived successfully',
        );
    }

    /**
     * approve request
     */
    public function approve(ApproveEventRequest $request, BoothRequest $boothRequest)
    {
        $force = $request->boolean('force', false);
        if (! $force) {
            $conflicts = $this->boothRequestService->getConflictingRequests($boothRequest);
            if ($conflicts->isNotEmpty()) {
                return errorResponse(
                    errors: [
                        'data' => BoothRequestResource::collection($conflicts)->response()->getData(true),
                    ],
                    message: 'Conflicting requests retrieved',
                    code: 409,
                );
            }
        }
        $this->boothRequestService->approve($boothRequest);

        return successResponse(
            data: null,
            message: 'booth request approved successfully',
        );
    }

    /**
     * reject request
     */
    public function reject(BoothRequest $boothRequest)
    {
        $this->boothRequestService->reject($boothRequest);

        return successResponse(
            data: null,
            message: 'request rejected successfully',
        );
    }
}
