<?php

namespace App\Policies;

use App\Enum\SystemUserType;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;

class EventPolicy
{
    public function viewReviews(SystemUser $user, Event $event): bool
    {
        return $event->query()->accessibleBy($user)
            ->whereKey($event->getKey())
            ->exists();
    }
  
    public function viewLeads(SystemUser $systemUser, Event $event): bool
    {
        return $this->isOwner($systemUser, $event) || $systemUser->type === SystemUserType::ADMIN;
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
    public function view(SystemUser $systemUser, Event $event): bool
    {
        return $this->isOwner($systemUser, $event) || $systemUser->type === SystemUserType::ADMIN;
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
    public function update(SystemUser $systemUser, Event $event): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(SystemUser $systemUser, Event $event): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(SystemUser $systemUser, Event $event): bool
    {
        return false;
    }
    /**
     * Determine whether the user can permanently delete the model.
     */
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
