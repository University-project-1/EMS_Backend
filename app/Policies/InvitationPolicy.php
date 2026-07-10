<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\SystemUser;

class InvitationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(SystemUser $systemUser, Invitation $invitation): bool
    {
        return $systemUser->email === $invitation->email
            || $systemUser->id === $invitation->sender_id;
    }

    /**
     * Determine whether the user can accept the invitation.
     */
    public function accept(SystemUser $systemUser, Invitation $invitation): bool
    {
        return $systemUser->email === $invitation->email;
    }

    /**
     * Determine whether the user can reject the invitation.
     */
    public function reject(SystemUser $systemUser, Invitation $invitation): bool
    {
        return $systemUser->email === $invitation->email;
    }

    public function delete(SystemUser $systemUserm, Invitation $invitation)
    {
        return $systemUserm->id === $invitation->sender_id;
    }
}
