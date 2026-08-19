<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Exhibitor\InvitaionResource;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Invitation;
use App\Services\SystemUser\Exhibitor\InvitationService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

#[Group('SystemUser/Exhibitor/Invitations')]
class InvitationController extends Controller
{
    public function __construct(
        public readonly InvitationService $invitationService,
    ) {}

    public function companyInvitations(Company $company)
    {
        Gate::authorize('manageInvitations', $company);
        $invitations = $company->invitations()->uniquePerEmail()
            ->with('sender')
            ->latest()
            ->paginate(10);

        return successResponse(
            data: InvitaionResource::collection($invitations),
            message: __('invitation.company_list_success'),
        );
    }

    public function boothInvitations(Booth $booth)
    {
        Gate::authorize('manageInvitations', $booth);
        $invitations = $booth->invitations()->uniquePerEmail()
            ->with('sender')
            ->latest()
            ->paginate(5);

        return successResponse(
            data: InvitaionResource::collection($invitations),
            message: __('invitation.booth_list_success'),
        );
    }

    public function show(Invitation $invitation)
    {
        $this->invitationService->check($invitation);

        return successResponse(
            data: new InvitaionResource($invitation->load(['sender', 'inviteable'])),
            message: __('invitation.show_success'),
        );
    }

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

    public function storeForBooth(Request $request, Booth $booth)
    {
        Gate::authorize('manageInvitations', $booth);
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);
        $this->invitationService->invite($booth, $request->user(), $validated['email']);

        return successResponse(
            message: __('invitation.send_success')
        );
    }

    public function destroy(Invitation $invitation)
    {
        Gate::authorize('delete', $invitation);
        $this->invitationService->delete($invitation);

        return successResponse(
            data: null,
            message: __('invitation.cancel_success'),
        );
    }

    public function approve(Invitation $invitation)
    {
        Gate::authorize('accept', $invitation);
        $this->invitationService->approve($invitation);

        return successResponse(
            message: __('invitation.approve_success'),
        );
    }

    public function reject(Invitation $invitation)
    {
        Gate::authorize('reject', $invitation);
        $this->invitationService->reject($invitation);

        return successResponse(
            message: __('invitation.reject_success'),
        );
    }
}
