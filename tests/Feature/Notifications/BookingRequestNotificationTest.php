<?php

use App\Channels\FcmChannel;
use App\Models\BoothRequest;
use App\Models\Event;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Admin\NewBookingRequestNotification;

it('builds event booking request notifications for administrators', function (): void {
    $event = new Event(['title' => 'Annual Technology Summit']);
    $event->forceFill(['id' => 42]);
    $notifiable = new SystemUser(['name' => 'Admin']);

    $notification = new NewBookingRequestNotification($event);

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe(['type' => 'event_booking_request_created', 'title' => 'notifications.booking_request_created_title', 'body' => 'notifications.booking_request_created_body', 'target_id' => 42, 'request_type' => 'event'])
        ->and($notification->toFcm($notifiable)['data'])->toBe(['type' => 'event_booking_request_created', 'target_id' => '42', 'request_type' => 'event']);
});

it('builds booth booking request notifications for administrators', function (): void {
    $boothRequest = new BoothRequest(['booth_id' => 8]);
    $boothRequest->forceFill(['id' => 17]);
    $notifiable = new SystemUser(['name' => 'Admin']);

    $notification = new NewBookingRequestNotification($boothRequest);

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe(['type' => 'booth_booking_request_created', 'title' => 'notifications.booking_request_created_title', 'body' => 'notifications.booking_request_created_body', 'target_id' => 17, 'request_type' => 'booth'])
        ->and($notification->toFcm($notifiable)['data'])->toBe(['type' => 'booth_booking_request_created', 'target_id' => '17', 'request_type' => 'booth']);
});
