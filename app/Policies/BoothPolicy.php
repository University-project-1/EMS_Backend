<?php

namespace App\Policies;

use App\Models\Booth;
use App\Models\SystemUser;

class BoothPolicy
{
    public function manageInvitations(SystemUser $systemUser, Booth $booth): bool
    {
        return $systemUser->booths()->where('booths.id', $booth->id)->exists()
            || $systemUser->companies()->where('companies.id', $booth->company_id)->exists();
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(SystemUser $systemUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(SystemUser $systemUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }
}
