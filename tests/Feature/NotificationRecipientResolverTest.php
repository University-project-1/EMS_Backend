<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\SystemUser;
use App\Services\Shared\NotificationRecipientResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('event owners resolves company members', function (): void {
    $owner = $this->createExhibitor();
    $company = createNotificationRecipientCompany();
    $company->systemUsers()->attach($owner->id, ['created_at' => now()]);
    $event = createNotificationRecipientEvent($company);

    $recipients = app(NotificationRecipientResolver::class)->eventOwners($event);

    expect($recipients->pluck('id')->all())->toBe([$owner->id]);
});

test('event owners resolves a direct system user organizer', function (): void {
    $organizer = $this->createExhibitor();
    $event = createNotificationRecipientEvent($organizer);

    $recipients = app(NotificationRecipientResolver::class)->eventOwners($event);

    expect($recipients->pluck('id')->all())->toBe([$organizer->id]);
});

function createNotificationRecipientCompany(): Company
{
    return Company::query()->create([
        'name' => 'Notification Recipient Company '.str()->random(4),
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for notification recipient tests.',
        'status' => Status::APPROVED->value,
    ]);
}

function createNotificationRecipientEvent(Company|SystemUser $owner): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-NOTIFY-'.str()->random(4),
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    return $owner->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Notification recipient event',
        'description' => 'An event used for notification recipient tests.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}
