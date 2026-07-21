<?php

namespace App\Policies;

use App\Enum\SystemUserType;
use App\Models\SystemUser;

class SystemUserPolicy
{
    public function viewAny(SystemUser $systemUser): bool
    {
        return $systemUser->type === SystemUserType::ADMIN;
    }

    public function view(SystemUser $systemUser, SystemUser $manager): bool
    {
        return $systemUser->type === SystemUserType::ADMIN
            && $manager->type === SystemUserType::EXHIBITOR;
    }
}
