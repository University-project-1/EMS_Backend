<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\ScanRequest;
use App\Http\Resources\Shared\LeadResource;
use App\Services\Mobile\LeadService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/Leads')]
class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leadService
    ){}

    public function store(ScanRequest $request){
        $lead = $this->leadService->registerScan(request()->user('mobile'), $request['token']);
        $lead->load('leadable');
        return successResponse(new LeadResource($lead));
    }
}
