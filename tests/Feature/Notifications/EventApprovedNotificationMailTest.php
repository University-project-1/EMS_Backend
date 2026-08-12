<?php

use App\Models\Event;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Exhibitor\EventApprovedNotification;

it('renders event approval email copy in English', function (): void {
    app()->setLocale('ar');

    $event = new Event([
        'title' => 'Annual Technology Summit',
    ]);
    $event->id = 42;

    $mail = (new EventApprovedNotification($event))->toMail(
        new SystemUser(['name' => 'Maya'])
    );

    expect($mail->subject)->toBe('Event Approved')
        ->and($mail->greeting)->toBe('Hello Maya,')
        ->and($mail->introLines)->toContain('We are pleased to inform you that your event "Annual Technology Summit" has been approved successfully.')
        ->and($mail->actionText)->toBe('View Event Details');
});
