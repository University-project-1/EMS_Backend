<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Exhibitor\InvitaionResource;
use App\Models\Booth;
use App\Models\Company;
use App\Services\SystemUser\Exhibitor\InvitationService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

#[Group('SystemUser/Exhibitor/Invitations')]
class InvitationController extends Controller
{
    public function __construct(
        public readonly InvitationService $invitationService,
    ){}

    /**
     * company Invitations
     */
    public function companyInvitations(Company $company){
        Gate::authorize('manageInvitations', $company);
        $invitations = $company->invitations()->with('sender')->latest()->paginate(5);
        return successResponse(
            data: InvitaionResource::collection($invitations),
            message: __('invitation.company_list_success'),
        );
    }
    /**
     * booth Invitations
     */
    public function boothInvitations(Booth $booth){
        Gate::authorize('manageInvitations', $booth);
        $invitations = $booth->invitations()->with('sender')->latest()->paginate(5);
        return successResponse(
            data: InvitaionResource::collection($invitations),
            message: __('invitation.booth_list_success'),
        );
    }
    /**
     * show
     */
    public function show(string $token){
        $invitation = $this->invitationService->getInvitationByToken($token);
        Gate::authorize('view', $invitation);
        return successResponse(
            data: new InvitaionResource($invitation),
            message: __('invitation.list_success'),
        );
    }

    /**
     * invite For Company
     */
    public function storeForCompany(Request $request, Company $company)
    {
        Gate::authorize('manageInvitations', $company);
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);
        $this->invitationService->invite($company, $request->user(), $validated['email']);

        return successResponse(
            message: __('invitation.send_success')
        );
    }
    /**
     * invite For Booth
     */
    public function storeForBooth(Request $request, Booth $booth)
    {
        Gate::authorize('manageInvitations', $booth);
        $validated = $request->validate([
            'email' => 'required|email|max:255'
        ]);
        $this->invitationService->invite($booth, $request->user(), $validated['email']);

        return successResponse(
            message: __('invitation.send_success')
        );
    }
    /**
     * approve
     */
    public function approve(string $token)
    {
        $invitation = $this->invitationService->getInvitationByToken($token);
        Gate::authorize('accept', $invitation);
        $this->invitationService->approve($invitation);

        return successResponse(
            message: __('invitation.approve_success'),
        );
    }
    /**
     * reject
     */
    public function reject(string $token)
    {
        $invitation = $this->invitationService->getInvitationByToken($token);
        Gate::authorize('reject', $invitation);
        $this->invitationService->reject($invitation);

        return successResponse(
            message: __('invitation.reject_success'),
        );
    }
}
