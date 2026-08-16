<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Shared\BoothRequestResource;
use App\Models\BoothRequest;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Exhibitor/BoothRequests')]
class BoothRequestController extends Controller
{
    #[QueryParameter('filter[status]', 'Filter booths by exact booth request status', required: false, type: 'string')]
    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 10')]
    /**
     * show all requests
     */
    public function index(Request $request)
    {
        $systemUserId = $request->user('system')->getKey();

        $boothRequests = QueryBuilder::for(BoothRequest::query())
            ->allowedFilters(AllowedFilter::exact('status'))
            ->where(function ($query) use ($systemUserId) {
                $query
                    ->where('system_user_id', $systemUserId)
                    ->orWhereHas(
                        'booth.systemUsers',
                        fn ($query) => $query->whereKey($systemUserId),
                    )
                    ->orWhereHas(
                        'company.systemUsers',
                        fn ($query) => $query->whereKey($systemUserId),
                    );
            })
            ->with(['company', 'company.logoMedia', 'booth', 'services.service'])
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return successResponse(
            data: BoothRequestResource::collection($boothRequests),
            message: 'Booth requests retrieved successfully',
        );
    }
}
