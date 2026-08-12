<?php

use App\Channels\FcmChannel;
use App\Enum\AnnouncementNotificationAction;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\SystemUser\Shared\AnnouncementNotification;

it('builds announcement notifications for database and FCM only', function (): void {
    $announcement = new Announcement([
        'title' => 'Registration is open',
        'description' => 'Reserve your place at the annual technology summit.',
    ]);
    $announcement->id = 17;

    $notification = new AnnouncementNotification(
        $announcement,
        AnnouncementNotificationAction::Created
    );
    $notifiable = new User;

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe([
            'type' => 'announcement',
            'title' => 'Registration is open',
            'body' => 'Reserve your place at the annual technology summit.',
            'target_id' => 17,
            'action' => 'created',
        ])
        ->and($notification->toFcm($notifiable))->toBe([
            'notification' => [
                'title' => 'Registration is open',
                'body' => 'Reserve your place at the annual technology summit.',
            ],
            'data' => [
                'type' => 'announcement',
                'target_id' => '17',
                'action' => 'created',
            ],
        ]);
});
