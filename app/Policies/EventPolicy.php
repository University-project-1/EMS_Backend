<?php

namespace App\Policies;

use App\Enum\SystemUserType;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;

class EventPolicy
{
    public function viewLeads(SystemUser $systemUser, Event $event): bool
    {
        return $this->isOwner($systemUser, $event) || $systemUser->type === SystemUserType::ADMIN;
    }

    public function viewAny(SystemUser $systemUser): bool
    {
        return true;
    }

    public function view(SystemUser $systemUser, Event $event): bool
    {
        return true;
    }

    public function create(SystemUser $systemUser): bool
    {
        return $systemUser->type === SystemUserType::EXHIBITOR;
    }

    public function update(SystemUser $systemUser, Event $event): bool
    {
        return $this->isOwner($systemUser, $event);
    }

    public function delete(SystemUser $systemUser, Event $event): bool
    {
        return $this->isOwner($systemUser, $event);
    }

    public function restore(SystemUser $systemUser, Event $event): bool
    {
        return false;
    }

    public function forceDelete(SystemUser $systemUser, Event $event): bool
    {
        return false;
    }
    private function isOwner(SystemUser $systemUser, Event $event): bool
    {
        if ($event->eventable_type === SystemUser::class) {
            return $event->eventable_id === $systemUser->id;
        }

        if ($event->eventable_type === Company::class) {
            return $systemUser->companies()->where('company_id', $event->eventable_id)->exists();
        }

        return false;
    }
}
