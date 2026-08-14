<?php

use App\Enum\BusinessSectors;
use App\Enum\LeadInterestNotificationType;
use App\Enum\Status;
use App\Enum\SystemUserType;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\Hall;
use App\Models\Lead;
use App\Models\LeadInterestNotificationDelivery;
use App\Models\SystemUser;
use App\Models\User;
use App\Notifications\Mobile\LeadInterestNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;

uses(DatabaseMigrations::class);

function createInterestCompany(): Company
{
    return Company::query()->create([
        'name' => 'Company '.fake()->unique()->numberBetween(1000, 9999),
        'business_sector' => BusinessSectors::TECH,
        'social_links' => [],
        'phone' => fake()->unique()->phoneNumber(),
        'year_founded' => 2020,
        'description' => 'A company used to verify lead-interest notifications.',
    ]);
}

function createInterestBooth(?Company $company = null): Booth
{
    $hall = Hall::query()->create([
        'number' => 'H-'.fake()->unique()->numberBetween(1000, 9999),
        'area' => 100,
    ]);

    return Booth::query()->create([
        'hall_id' => $hall->getKey(),
        'company_id' => $company?->getKey(),
        'number' => 'B-'.fake()->unique()->numberBetween(1000, 9999),
        'area' => 10,
        'price' => 100,
    ]);
}

function createInterestEvent(Company|SystemUser $organizer): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'EH-'.fake()->unique()->numberBetween(1000, 9999),
        'area' => 100,
        'price_per_hour' => 500,
    ]);

    return Event::query()->create([
        'eventable_type' => $organizer::class,
        'eventable_id' => $organizer->getKey(),
        'event_hall_id' => $eventHall->getKey(),
        'title' => 'Interest Test Event',
        'description' => 'An event used to verify lead-interest notifications.',
        'status' => Status::PENDING,
        'start_at' => now()->addDay(),
        'duration' => 60,
        'end_at' => now()->addDay()->addHour(),
        'price' => 0,
    ]);
}

it('notifies company leads when a company is assigned to a booth without duplicates', function (): void {
    Notification::fake();

    $company = createInterestCompany();
    $interestedUser = User::factory()->create();
    $existingBooth = createInterestBooth($company);
    Lead::query()->create([
        'user_id' => $interestedUser->getKey(),
        'leadable_type' => Booth::class,
        'leadable_id' => $existingBooth->getKey(),
    ]);

    $newBooth = createInterestBooth();
    $newBooth->update(['company_id' => $company->getKey()]);

    Notification::assertSentTo(
        $interestedUser,
        LeadInterestNotification::class,
        fn (LeadInterestNotification $notification): bool => $notification->type === LeadInterestNotificationType::COMPANY_BOOTH_CREATED,
    );
    expect(LeadInterestNotificationDelivery::query()->count())->toBe(1);

    $newBooth->update(['company_id' => null]);
    $newBooth->update(['company_id' => $company->getKey()]);

    Notification::assertSentToTimes($interestedUser, LeadInterestNotification::class, 1);
    expect(LeadInterestNotificationDelivery::query()->count())->toBe(1);
});

it('notifies company leads when one of its events is approved', function (): void {
    Notification::fake();

    $company = createInterestCompany();
    $interestedUser = User::factory()->create();
    $existingBooth = createInterestBooth($company);
    Lead::query()->create([
        'user_id' => $interestedUser->getKey(),
        'leadable_type' => Booth::class,
        'leadable_id' => $existingBooth->getKey(),
    ]);

    $event = createInterestEvent($company);
    $event->update(['status' => Status::APPROVED]);

    Notification::assertSentTo(
        $interestedUser,
        LeadInterestNotification::class,
        fn (LeadInterestNotification $notification): bool => $notification->type === LeadInterestNotificationType::COMPANY_EVENT_CREATED,
    );
});

it('notifies organizer event leads when the organizer publishes another event', function (): void {
    Notification::fake();

    $organizer = SystemUser::query()->create([
        'name' => 'Independent Organizer',
        'email' => fake()->unique()->safeEmail(),
        'type' => SystemUserType::EXHIBITOR,
    ]);
    $interestedUser = User::factory()->create();
    $existingEvent = createInterestEvent($organizer);
    Lead::query()->create([
        'user_id' => $interestedUser->getKey(),
        'leadable_type' => Event::class,
        'leadable_id' => $existingEvent->getKey(),
    ]);

    $event = createInterestEvent($organizer);
    $event->update(['status' => Status::APPROVED]);

    Notification::assertSentTo(
        $interestedUser,
        LeadInterestNotification::class,
        fn (LeadInterestNotification $notification): bool => $notification->type === LeadInterestNotificationType::ORGANIZER_EVENT_CREATED,
    );
});
