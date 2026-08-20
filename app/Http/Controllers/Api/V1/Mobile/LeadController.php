<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\ScanRequest;
use App\Http\Resources\Mobile\ScanHistoryResource;
use App\Http\Resources\Shared\LeadResource;
use App\Services\Mobile\LeadService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;

#[Group('Visitor/Leads')]
class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leadService
    ) {}

    public function store(ScanRequest $request)
    {
        $lead = $this->leadService->registerScan(request()->user('mobile'), $request['token']);
        $lead->load('leadable');

        return successResponse(new LeadResource($lead));
    }

    /**
     * Leads history.
     */
    #[QueryParameter('per_page', 'Number of events per page (maximum 100)', required: false, type: 'integer')]
    public function index()
    {
        $leads = request()
            ->user('mobile')
            ->leads()
            ->with('leadable')
            ->latest()
            ->cursorPaginate(request()->integer('per_page', 10));

        return successResponse(ScanHistoryResource::collection($leads));
    }
}
