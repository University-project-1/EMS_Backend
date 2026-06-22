<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Filter\DateFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Shared\BoothRequestResource;
use App\Models\BoothRequest;
use App\Services\SystemUser\Admin\BoothRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BoothRequestController extends Controller
{
    public function __construct(
        public readonly BoothRequestService $boothRequestService,
    ){}
    public function index(){
        $boothRequests = QueryBuilder::for(BoothRequest::class)
        ->allowedFilters(
            'name',
            AllowedFilter::exact('status'),
            AllowedFilter::custom('created_date', new DateFilter(), 'created_at'),
        )
        ->allowedSorts('created_at')
        ->allowedIncludes('company')
        ->paginate(request()->per_page(5));


        return successResponse(
            data: BoothRequestResource::collection($boothRequests),
            message: 'booth requests retrived successfully',
        );
    }

    public function show(BoothRequest $boothRequest){
        $boothRequest->load(['systemUser', 'company', 'company.logoMedia', 'company.galleryMedia', 'booth', 'services']);
        return successResponse(
            data: new BoothRequestResource($boothRequest),
            message: 'booth request retrived successfully',
        );
    }

    public function approve(Request $request, BoothRequest $boothRequest){
        $force = $request->boolean('force', false);
        if (! $force) {
            $conflicts = $this->boothRequestService->getConflictingRequests($boothRequest);
            if($conflicts->isNotEmpty()){
                return errorResponse(
                    errors: BoothRequestResource::collection($conflicts)->response()->getData(true),
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

    public function reject(BoothRequest $boothRequest){
        $this->boothRequestService->reject($boothRequest);
        return successResponse(
            data: null,
            message: 'request rejected successfully',
        );
    }
}
