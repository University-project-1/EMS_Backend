<?php

namespace App\Services\Shared;

use App\Enum\SystemUserType;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;
use Illuminate\Support\Collection;

class NotificationRecipientResolver
{
    public function admins(): Collection
    {
        return SystemUser::query()->where('type', SystemUserType::ADMIN)->get();
    }

    public function eventOwners(Event $event): Collection
    {
        $event->loadMissing('eventable.systemUsers');

        return match (true) {
            $event->eventable instanceof Company => $event->eventable->systemUsers,
            $event->eventable instanceof SystemUser => collect([$event->eventable]),
            default => collect(),
        };
    }

    public function boothCompanyMembers(Booth $booth): Collection
    {
        $booth->loadMissing('company.systemUsers');

        $company = $booth->company;

        return $company instanceof Company ? $company->systemUsers : collect();
    }

    public function boothRequestRecipients(BoothRequest $boothRequest): Collection
    {
        $boothRequest->loadMissing('company.systemUsers', 'systemUser');

        $company = $boothRequest->company;
        $members = $company instanceof Company ? $company->systemUsers : collect();

        return $members
            ->push($boothRequest->systemUser)
            ->filter()
            ->unique('id')
            ->values();
    }
}
