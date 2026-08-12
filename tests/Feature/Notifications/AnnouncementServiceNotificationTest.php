<?php

use App\DTOs\SystemUser\AnnouncementDTO;
use App\DTOs\SystemUser\UpdateAnnouncementDTO;
use App\Enum\AnnouncementNotificationAction;
use App\Enum\SystemUserType;
use App\Models\SystemUser;
use App\Models\User;
use App\Notifications\SystemUser\Shared\AnnouncementNotification;
use App\Services\SystemUser\Admin\AnnouncementService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;

uses(DatabaseMigrations::class);

it('notifies visitors only when the announcement targets visitors', function (): void {
    Notification::fake();

    $visitor = User::factory()->create();
    $exhibitor = SystemUser::query()->create([
        'name' => 'Exhibitor',
        'email' => 'exhibitor@example.test',
        'type' => SystemUserType::EXHIBITOR,
    ]);

    $announcement = app(AnnouncementService::class)->create(
        new AnnouncementDTO(
            title: 'Visitors update',
            description: 'This message is for visitors only.',
            receiver: 'visitors',
            isActive: true,
            media: null
        )
    );

    Notification::assertSentTo(
        $visitor,
        AnnouncementNotification::class,
        fn (AnnouncementNotification $notification): bool => $notification->announcement->is($announcement)
            && $notification->action === AnnouncementNotificationAction::Created
    );
    Notification::assertNotSentTo($exhibitor, AnnouncementNotification::class);
});

it('notifies visitors and exhibitors when an all-audience announcement is updated', function (): void {
    Notification::fake();

    $visitor = User::factory()->create();
    $exhibitor = SystemUser::query()->create([
        'name' => 'Exhibitor',
        'email' => 'exhibitor@example.test',
        'type' => SystemUserType::EXHIBITOR,
    ]);
    $admin = SystemUser::query()->create([
        'name' => 'Admin',
        'email' => 'admin@example.test',
        'type' => SystemUserType::ADMIN,
    ]);

    $service = app(AnnouncementService::class);
    $announcement = $service->create(
        new AnnouncementDTO(
            title: 'Initial update',
            description: 'Initial content.',
            receiver: 'all',
            isActive: false,
            media: null
        )
    );

    Notification::fake();
    $announcement = $service->edit(
        $announcement,
        new UpdateAnnouncementDTO(
            title: 'Updated announcement',
            description: 'Updated content.',
            receiver: 'all',
            isActive: true,
            media: null,
            payload: [
                'title' => 'Updated announcement',
                'description' => 'Updated content.',
                'receiver' => 'all',
                'is_active' => true,
            ]
        )
    );

    foreach ([$visitor, $exhibitor] as $recipient) {
        Notification::assertSentTo(
            $recipient,
            AnnouncementNotification::class,
            fn (AnnouncementNotification $notification): bool => $notification->announcement->is($announcement)
                && $notification->action === AnnouncementNotificationAction::Updated
        );
    }

    Notification::assertNotSentTo($admin, AnnouncementNotification::class);
});
