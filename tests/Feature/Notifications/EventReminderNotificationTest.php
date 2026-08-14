<?php

use App\Channels\FcmChannel;
use App\Models\Event;
use App\Models\User;
use App\Notifications\Mobile\EventReminderNotification;

it('builds event reminders for database and FCM only', function (): void {
    $event = new Event(['title' => 'Backend Summit']);
    $notifiable = new User;
    $notification = new EventReminderNotification($event);

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe([
            'type' => 'event_reminder',
            'title' => 'notifications.event_reminder_title',
            'body' => 'notifications.event_reminder_body',
            'target_id' => null,
        ])
        ->and($notification->toFcm($notifiable))->toBe([
            'notification' => [
                'title' => 'Event Starting Soon',
                'body' => 'Backend Summit starts in 15 minutes.',
            ],
            'data' => [
                'type' => 'event_reminder',
                'target_id' => '',
            ],
        ]);
});
