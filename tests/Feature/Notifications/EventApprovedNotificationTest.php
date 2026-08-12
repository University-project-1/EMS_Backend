<?php

use App\Channels\FcmChannel;
use App\Models\Event;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Exhibitor\EventApprovedNotification;

it('builds the event approval payload for all delivery channels', function (): void {
    app()->setLocale('en');

    $event = new Event([
        'title' => 'Annual Technology Summit',
    ]);
    $event->id = 42;

    $notification = new EventApprovedNotification($event);
    $notifiable = new SystemUser([
        'name' => 'Maya',
    ]);

    expect($notification->via($notifiable))->toBe(['database', 'mail', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe([
            'type' => 'event_approved',
            'title' => 'notifications.event_approved_title',
            'body' => 'notifications.event_approved_body',
            'target_id' => 42,
        ])
        ->and($notification->toFcm($notifiable))->toBe([
            'notification' => [
                'title' => 'Event Approved',
                'body' => 'Your event "Annual Technology Summit" has been approved successfully.',
            ],
            'data' => [
                'type' => 'event_approved',
                'target_id' => '42',
            ],
        ]);
});
