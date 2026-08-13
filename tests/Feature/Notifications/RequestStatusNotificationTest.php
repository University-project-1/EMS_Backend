<?php

use App\Channels\FcmChannel;
use App\Enum\Status;
use App\Models\BoothRequest;
use App\Models\Event;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Exhibitor\BoothRequestStatusNotification;
use App\Notifications\SystemUser\Exhibitor\EventRequestStatusNotification;

it('builds approved and rejected event status notifications', function (): void {
    $event = new Event(['title' => 'Annual Technology Summit']);
    $event->forceFill(['id' => 42]);
    $notifiable = new SystemUser(['name' => 'Alex']);

    $notification = new EventRequestStatusNotification($event, Status::REJECTED);

    expect($notification->via($notifiable))->toBe(['database', 'mail', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe(['type' => 'event_rejected', 'title' => 'notifications.event_rejected_title', 'body' => 'notifications.event_rejected_body', 'target_id' => 42])
        ->and($notification->toFcm($notifiable))->toBe(['notification' => ['title' => 'Event Request Rejected', 'body' => 'Your event Annual Technology Summit has been rejected.'], 'data' => ['type' => 'event_rejected', 'target_id' => '42']]);
});

it('builds approved and rejected booth status notifications', function (): void {
    $request = new BoothRequest(['booth_id' => 8]);
    $request->forceFill(['id' => 17]);
    $notifiable = new SystemUser(['name' => 'Alex']);

    $notification = new BoothRequestStatusNotification($request, Status::REJECTED);

    expect($notification->via($notifiable))->toBe(['database', 'mail', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe(['type' => 'booth_rejected', 'title' => 'notifications.booth_rejected_title', 'body' => 'notifications.booth_rejected_body', 'target_id' => 17])
        ->and($notification->toFcm($notifiable))->toBe(['notification' => ['title' => 'Booth Request Rejected', 'body' => 'Your booth booking request has been rejected.'], 'data' => ['type' => 'booth_rejected', 'target_id' => '17']]);
});
