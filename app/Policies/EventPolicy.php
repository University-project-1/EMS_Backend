<?php

namespace App\Policies;

use App\Enum\SystemUserType;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    public function viewLeads(SystemUser $systemUser, Event $event): bool
    {
        return $systemUser->type === SystemUserType::ADMIN
            || $systemUser->events()->where('events.id', $event->id)->exists()
            || $systemUser->companies()->where('companies.id', $event->eventable_id)->exists() && $event->eventable_type instanceof Company;
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
}
