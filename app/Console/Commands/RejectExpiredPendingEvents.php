<?php

namespace App\Console\Commands;

use App\Enum\RequestRejectionReason;
use App\Enum\Status;
use App\Models\Event;
use App\Notifications\SystemUser\Exhibitor\EventRequestStatusNotification;
use App\Services\Shared\NotificationRecipientResolver;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

#[Signature('events:reject-expired-pending')]
#[Description('Reject pending event requests whose start time has passed.')]
class RejectExpiredPendingEvents extends Command
{
    public function __construct(
        private readonly NotificationRecipientResolver $notificationRecipients,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $rejectedEvents = 0;
        $now = now();

        Event::query()
            ->where('status', Status::PENDING->value)
            ->where('start_at', '<', $now)
            ->orderBy('id')
            ->chunkById(100, function ($events) use (&$rejectedEvents, $now): void {
                $events->each(function (Event $event) use (&$rejectedEvents, $now): void {
                    DB::transaction(function () use ($event, $now, &$rejectedEvents): void {
                        $event->refresh();

                        if ($event->status !== Status::PENDING || $event->start_at->greaterThanOrEqualTo($now)) {
                            return;
                        }

                        $event->update(['status' => Status::REJECTED]);
                        $rejectedEvents++;

                        DB::afterCommit(function () use ($event): void {
                            Notification::send(
                                $this->notificationRecipients->eventOwners($event)->filter()->unique('id'),
                                new EventRequestStatusNotification(
                                    $event,
                                    Status::REJECTED,
                                    RequestRejectionReason::EVENT_EXPIRED,
                                ),
                            );
                        });
                    });
                });
            });

        $this->info("Rejected {$rejectedEvents} expired pending event request(s).");

        return self::SUCCESS;
    }
}
