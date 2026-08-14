<?php

namespace App\Observers;

use App\Enum\LeadInterestNotificationType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;
use App\Services\Mobile\LeadInterestAudienceResolver;
use App\Services\Mobile\LeadInterestNotificationDispatcher;
use Illuminate\Support\Facades\DB;

class EventObserver
{
    public function __construct(
        private readonly LeadInterestAudienceResolver $audienceResolver,
        private readonly LeadInterestNotificationDispatcher $notificationDispatcher,
    ) {}

    public function updated(Event $event): void
    {
        if (! $event->wasChanged('status') || $event->status !== Status::APPROVED) {
            return;
        }

        DB::afterCommit(function () use ($event): void {
            $event->loadMissing('eventable');

            match (true) {
                $event->eventable instanceof Company => $this->notificationDispatcher->send(
                    $this->audienceResolver->forCompany($event->eventable),
                    $event,
                    LeadInterestNotificationType::COMPANY_EVENT_CREATED,
                ),
                $event->eventable instanceof SystemUser => $this->notificationDispatcher->send(
                    $this->audienceResolver->forOrganizer($event->eventable),
                    $event,
                    LeadInterestNotificationType::ORGANIZER_EVENT_CREATED,
                ),
                default => null,
            };
        });
    }
}
