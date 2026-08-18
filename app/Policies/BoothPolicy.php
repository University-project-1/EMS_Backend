<?php

namespace App\Policies;

use App\Enum\SystemUserType;
use App\Models\Booth;
use App\Models\SystemUser;

class BoothPolicy
{
    public function viewLeads(SystemUser $systemUser, Booth $booth): bool
    {
        return $this->manageInvitations($systemUser, $booth);
    }

    public function viewReviews(SystemUser $systemUser, Booth $booth): bool
    {
        return $this->manageInvitations($systemUser, $booth);
    }

    public function manageInvitations(SystemUser $systemUser, Booth $booth): bool
    {
        if ($systemUser->type === SystemUserType::ADMIN) {
            return true;
        }

        return $systemUser->booths()->whereKey($booth->getKey())->exists()
            || ($booth->company_id && $systemUser->companies()->whereKey($booth->company_id)->exists());
    }

    public function viewAny(SystemUser $systemUser): bool
    {
        return false;
    }
    
  public function view(SystemUser $systemUser, Booth $booth): bool
    {
        return $this->manageInvitations($systemUser, $booth);
    }

    public function create(SystemUser $systemUser): bool
    {
        return false;
    }

    public function update(SystemUser $systemUser, Booth $booth): bool
    {
        return $this->manageInvitations($systemUser, $booth);
    }

    public function delete(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }

    public function restore(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }

    public function forceDelete(SystemUser $systemUser, Booth $booth): bool
    {
        return false;
    }
}
