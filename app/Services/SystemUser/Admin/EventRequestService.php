<?php

namespace App\Services\SystemUser\Admin;

use App\Enum\RequestRejectionReason;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Notifications\SystemUser\Exhibitor\EventCancellationNotification;
use App\Notifications\SystemUser\Exhibitor\EventPaymentReminderNotification;
use App\Notifications\SystemUser\Exhibitor\EventRequestStatusNotification;
use App\Services\Shared\NotificationRecipientResolver;
use App\Services\Shared\QrCodeService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventRequestService
{
    public function __construct(
        private QrCodeService $qrCodeService,
        private readonly NotificationRecipientResolver $notificationRecipients,
    ) {}

    public function getConflictingRequests(Event $event): LengthAwarePaginator
    {
        return Event::query()
            ->where('id', '!=', $event->id)
            ->where('event_hall_id', $event->event_hall_id)
            ->where('status', Status::PENDING->value)
            ->where('start_at', '<', $event->end_at)
            ->where('end_at', '>', $event->start_at)
            ->with('media', 'eventable')
            ->paginate(perPage: 3, page: 1);
    }

    public function approve(Event $event): void
    {
        DB::transaction(function () use ($event): void {
            EventHall::query()->whereKey($event->event_hall_id)->lockForUpdate()->firstOrFail();
            $event->refresh();

            if ($event->status !== Status::PENDING) {
                throw new HttpException(400, __('validation.invalid_status'));
            }

            $hasApprovedConflict = Event::query()
                ->where('id', '!=', $event->id)
                ->where('event_hall_id', $event->event_hall_id)
                ->where('status', Status::APPROVED->value)
                ->where('start_at', '<', $event->end_at)
                ->where('end_at', '>', $event->start_at)
                ->exists();

            if ($hasApprovedConflict) {
                throw new HttpException(409, __('validation.hall_unavailable'));
            }

            $conflictingEvents = Event::query()
                ->where('id', '!=', $event->id)
                ->where('event_hall_id', $event->event_hall_id)
                ->where('status', Status::PENDING->value)
                ->where('start_at', '<', $event->end_at)
                ->where('end_at', '>', $event->start_at)
                ->with('eventable')
                ->get();

            $token = 'E-'.$event->id.'-'.Str::random(10);
            $event->update([
                'status' => Status::APPROVED,
                'qr_token' => $token,
            ]);
            $event->addMediaFromString($this->qrCodeService->generateSvg($token))
                ->usingFileName("{$token}.svg")
                ->toMediaCollection('qr_code');

            if ($event->eventable_type === Company::class) {
                $event->eventable->update(['status' => Status::APPROVED]);
            }

            $conflictingEvents->each(
                fn (Event $conflictingEvent) => $conflictingEvent->update(['status' => Status::REJECTED])
            );

            DB::afterCommit(function () use ($event, $conflictingEvents): void {
                Notification::send(
                    $this->notificationRecipients->eventOwners($event)->filter()->unique('id'),
                    new EventRequestStatusNotification($event, Status::APPROVED),
                );

                $this->notifyConflictingEventOwners($conflictingEvents);
            });
        });
    }

    public function sendPaymentReminder(Event $event): void
    {
        if ($event->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        Notification::send($this->notificationRecipients->eventOwners($event)->filter()->unique('id'), new EventPaymentReminderNotification($event));
    }
    public function cancelApprovedEvent(Event $event): Event
    {
        return DB::transaction(function () use ($event): Event {
            EventHall::query()->whereKey($event->event_hall_id)->lockForUpdate()->firstOrFail();
            $event->refresh();

            if ($event->status !== Status::APPROVED) {
                throw new HttpException(400, __('validation.invalid_status'));
            }

            $event->update([
                'status' => Status::CANCELED,
                'qr_token' => null,
            ]);
            $event->clearMediaCollection('qr_code');

            DB::afterCommit(function () use ($event): void {
                Notification::send(
                    $this->notificationRecipients->eventOwners($event)->filter()->unique('id'),
                    new EventCancellationNotification($event),
                );
            });

            return $event;
        });
    }

    public function reject(Event $event): void
    {
        if ($event->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        DB::transaction(function () use ($event): void {
            $event->update(['status' => Status::REJECTED]);

            DB::afterCommit(function () use ($event): void {
                Notification::send(
                    $this->notificationRecipients->eventOwners($event)->filter()->unique('id'),
                    new EventRequestStatusNotification($event, Status::REJECTED),
                );
            });
        });
    }

    private function notifyConflictingEventOwners(Collection $events): void
    {
        $events->each(function (Event $event): void {
            Notification::send(
                $this->notificationRecipients->eventOwners($event)->filter()->unique('id'),
                new EventRequestStatusNotification(
                    $event,
                    Status::REJECTED,
                    RequestRejectionReason::EVENT_SCHEDULE_CONFLICT,
                ),
            );
        });
    }
}
