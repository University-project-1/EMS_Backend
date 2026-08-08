<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\LeadResource;
use App\Models\Booth;
use App\Services\SystemUser\Exhibitor\LeadService;
use Dedoc\Scramble\Attributes\Group;
use Event;
use Illuminate\Support\Facades\Gate;

#[Group('SystemUser/Exhibitor/Leads')]
class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leadService
    ){}

    /**
     * booth Leads
     */
    public function boothLeads(Booth $booth){
        Gate::authorize('viewLeads', $booth);
        $result = $this->leadService->getLeadStatistics($booth);
        return successResponse([
            'latest_visitors' => LeadResource::collection($result['latest_visitors']),
            'weekly_stats' => $result['weekly_stats']
        ]);
    }

    /**
     * event Leads
     */
    public function eventLeads(Event $event){
        Gate::authorize('viewLeads', $event);
        $result = $this->leadService->getLeadStatistics($event);
        $result['visitors'] = LeadResource::collection($result['visitors']);
        return successResponse($result);
    }
}
