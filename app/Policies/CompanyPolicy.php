<?php

namespace App\Policies;

use App\Enum\Status;
use App\Enum\SystemUserType;
use App\Models\Company;
use App\Models\SystemUser;

class CompanyPolicy
{
    public function manageInvitations(SystemUser $systemUser, Company $company): bool
    {
        return $systemUser->companies()->where('companies.id', $company->id)->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(SystemUser $user): bool
    {
        return true;
    }

    public function view(SystemUser $user, Company $company): bool
    {
        if ($user->type === SystemUserType::ADMIN) {
            return true;
        }

        return $company->status === Status::APPROVED ||
               $company->systemUsers()->where('system_user_id', $user->id)->exists();
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
    public function update(SystemUser $systemUser, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(SystemUser $systemUser, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(SystemUser $systemUser, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(SystemUser $systemUser, Company $company): bool
    {
        return false;
    }
}
