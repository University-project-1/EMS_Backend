<?php

namespace App\Policies;

use App\Enum\SystemUserType;
use App\Models\Booth;
use App\Models\SystemUser;

class BoothPolicy
{
    public function viewLeads(SystemUser $systemUser, Booth $booth): bool
    {
        return $this->manageInvitations($systemUser, $booth) || $systemUser->type === SystemUserType::ADMIN ;
    }

    public function manageInvitations(SystemUser $systemUser, Booth $booth): bool
    {
        return $systemUser->booths()->where('booths.id', $booth->id)->exists()
            || $booth->company_id && $systemUser->companies()->where('company_id', $booth->company_id)->exists();
    }

    public function viewAny(SystemUser $systemUser): bool
    {
        return $systemUser->type === SystemUserType::EXHIBITOR || $systemUser->type === SystemUserType::ADMIN;
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
