<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Exhibitor\InvitaionResource;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Invitation;
use App\Services\SystemUser\Exhibitor\InvitationService;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __construct(
        public readonly InvitationService $invitationService,
    ){}

    public function companyInvitations(Company $company){
        //TODO : Gate - is exhibitor in the company
        $invitations = $company->invitations()->with('sender')->latest()->paginate(5);
        return successResponse(
            data: InvitaionResource::collection($invitations),
            message: __('invitation.company_list_success'),
        );
    }
    public function boothInvitations(Booth $company){
        //TODO : Gate - is exhibitor in the booth
        $invitations = $company->invitations()->with('sender')->latest()->paginate(5);
        return successResponse(
            data: InvitaionResource::collection($invitations),
            message: __('invitation.booth_list_success'),
        );
    }
    public function show(string $token){
        $invitation = $this->invitationService->getInvitationByToken($token);
        return successResponse(
            data: new InvitaionResource($invitation),
            message: __('invitation.list_success'),
        );
    }
    public function storeForCompany(Request $request, Company $company)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255'
        ]);

        // TODO: Gate

        $this->invitationService->invite($company, $request->user(), $validated['email']);

        return successResponse(
            message: __('invitation.send_success')
        );
    }
    public function storeForBooth(Request $request, Booth $booth)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255'
        ]);

        // TODO: Gate
        $this->invitationService->invite($booth, $request->user(), $validated['email']);

        return successResponse(
            message: __('invitation.send_success')
        );
    }
    public function approve(string $token){
        $this->invitationService->approve($token);
        return successResponse(
            message: __('invitation.approve_success'),
        );
    }
    public function reject(string $token){
        $this->invitationService->reject($token);
        return successResponse(
            message: __('invitation.reject_success'),
        );
    }
}
