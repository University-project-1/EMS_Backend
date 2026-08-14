<?php

use App\Channels\FcmChannel;
use App\Enum\LeadInterestNotificationType;
use App\Models\Booth;
use App\Models\Event;
use App\Models\User;
use App\Notifications\Mobile\LeadInterestNotification;

it('builds company booth interest notifications', function (): void {
    $booth = new Booth(['number' => 'B-12']);
    $notifiable = new User;
    $notification = new LeadInterestNotification($booth, LeadInterestNotificationType::COMPANY_BOOTH_CREATED);

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe([
            'type' => 'company_booth_created',
            'title' => 'notifications.company_booth_created_title',
            'body' => 'notifications.company_booth_created_body',
            'target_id' => null,
            'target_type' => Booth::class,
        ])
        ->and($notification->toFcm($notifiable)['notification']['body'])->toBe('A company you are interested in has booked Booth B-12.');
});

it('builds company event interest notifications', function (): void {
    $event = new Event(['title' => 'Product Launch']);
    $notifiable = new User;
    $notification = new LeadInterestNotification($event, LeadInterestNotificationType::COMPANY_EVENT_CREATED);

    expect($notification->toDatabase($notifiable)['type'])->toBe('company_event_created')
        ->and($notification->toFcm($notifiable)['notification']['body'])->toBe('A company you are interested in has created the event Product Launch.');
});

it('builds organizer event interest notifications', function (): void {
    $event = new Event(['title' => 'Founder Talk']);
    $notifiable = new User;
    $notification = new LeadInterestNotification($event, LeadInterestNotificationType::ORGANIZER_EVENT_CREATED);

    expect($notification->toDatabase($notifiable)['type'])->toBe('organizer_event_created')
        ->and($notification->toFcm($notifiable)['notification']['body'])->toBe('An organizer you are interested in has created the event Founder Talk.');
});

it('rejects unsupported content and notification type combinations', function (): void {
    new LeadInterestNotification(
        new Booth(['number' => 'B-12']),
        LeadInterestNotificationType::COMPANY_EVENT_CREATED,
    );
})->throws(InvalidArgumentException::class);
