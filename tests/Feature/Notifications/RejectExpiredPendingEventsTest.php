<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\RequestRejectionReason;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Notifications\SystemUser\Exhibitor\EventRequestStatusNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-08-17 12:00:00', 'UTC'));
    Notification::fake();
});

afterEach(function (): void {
    Carbon::setTestNow();
});

test('scheduled command rejects only expired pending events and notifies their owners once', function (): void {
    $administrator = $this->createAdministrator();
    $owner = $this->createExhibitor();
    $company = createExpiredEventCompany('Expired Event Company');
    $company->systemUsers()->attach($owner->id, ['created_at' => now()]);
    $hall = createExpiredEventHall();

    $expiredEvent = createExpiredEvent($company, $hall, 'Expired pending event', Status::PENDING, now()->subMinute());
    $futureEvent = createExpiredEvent($company, $hall, 'Future pending event', Status::PENDING, now()->addHour());
    $approvedPastEvent = createExpiredEvent($company, $hall, 'Approved past event', Status::APPROVED, now()->subHour());

    $this->artisan('events:reject-expired-pending')->assertSuccessful();

    $this->assertDatabaseHas('events', [
        'id' => $expiredEvent->id,
        'status' => Status::REJECTED->value,
    ]);
    $this->assertDatabaseHas('events', [
        'id' => $futureEvent->id,
        'status' => Status::PENDING->value,
    ]);
    $this->assertDatabaseHas('events', [
        'id' => $approvedPastEvent->id,
        'status' => Status::APPROVED->value,
    ]);

    Notification::assertSentTo(
        $owner,
        EventRequestStatusNotification::class,
        fn (EventRequestStatusNotification $notification): bool => $notification->event->is($expiredEvent)
            && $notification->status === Status::REJECTED
            && $notification->rejectionReason === RequestRejectionReason::EVENT_EXPIRED,
    );
    Notification::assertNotSentTo($administrator, EventRequestStatusNotification::class);

    $this->artisan('events:reject-expired-pending')->assertSuccessful();

    Notification::assertSentToTimes($owner, EventRequestStatusNotification::class, 1);
});

function createExpiredEventCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for expired event rejection tests.',
        'status' => Status::APPROVED->value,
    ]);
}

function createExpiredEventHall(): EventHall
{
    return EventHall::query()->create([
        'number' => 'HALL-EXPIRED',
        'area' => 250,
        'price_per_hour' => 100,
    ]);
}

function createExpiredEvent(Company $company, EventHall $hall, string $title, Status $status, Carbon $startAt): Event
{
    return $company->events()->create([
        'event_hall_id' => $hall->id,
        'title' => $title,
        'description' => 'An event used for expired request tests.',
        'type' => EventType::OTHER->value,
        'status' => $status->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}
