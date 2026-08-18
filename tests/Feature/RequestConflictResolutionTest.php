<?php

use App\DTOs\SystemUser\EventDTO;
use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\RequestRejectionReason;
use App\Enum\Status;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\Hall;
use App\Notifications\SystemUser\Exhibitor\BoothRequestStatusNotification;
use App\Notifications\SystemUser\Exhibitor\EventRequestStatusNotification;
use App\Services\SystemUser\Admin\BoothRequestService;
use App\Services\SystemUser\Admin\EventRequestService;
use App\Services\SystemUser\Exhibitor\EventService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-08-17 10:00:00', 'UTC'));
    Notification::fake();
});

afterEach(function (): void {
    Carbon::setTestNow();
});

test('approving an event rejects overlapping pending events and notifies only affected owners', function (): void {
    $administrator = $this->createAdministrator();
    $approvedOwner = $this->createExhibitor();
    $conflictingOwner = $this->createExhibitor();
    $approvedCompany = createConflictCompany('Approved Event Company');
    $conflictingCompany = createConflictCompany('Conflicting Event Company');
    $approvedCompany->systemUsers()->attach($approvedOwner->id, ['created_at' => now()]);
    $conflictingCompany->systemUsers()->attach($conflictingOwner->id, ['created_at' => now()]);
    $eventHall = createConflictEventHall();

    $approvedEvent = createConflictEvent(
        $approvedCompany,
        $eventHall,
        'Accepted event',
        Status::PENDING,
        now()->addDay(),
        2,
    );
    $conflictingEvent = createConflictEvent(
        $conflictingCompany,
        $eventHall,
        'Overlapping event',
        Status::PENDING,
        now()->addDay()->addHour(),
        2,
    );

    app(EventRequestService::class)->approve($approvedEvent);

    $this->assertDatabaseHas('events', ['id' => $approvedEvent->id, 'status' => Status::APPROVED->value]);
    $this->assertDatabaseHas('events', ['id' => $conflictingEvent->id, 'status' => Status::REJECTED->value]);

    Notification::assertSentTo(
        $approvedOwner,
        EventRequestStatusNotification::class,
        fn (EventRequestStatusNotification $notification): bool => $notification->event->is($approvedEvent)
            && $notification->status === Status::APPROVED
            && $notification->rejectionReason === null,
    );
    Notification::assertSentTo(
        $conflictingOwner,
        EventRequestStatusNotification::class,
        fn (EventRequestStatusNotification $notification): bool => $notification->event->is($conflictingEvent)
            && $notification->status === Status::REJECTED
            && $notification->rejectionReason === RequestRejectionReason::EVENT_SCHEDULE_CONFLICT,
    );
    Notification::assertNotSentTo($administrator, EventRequestStatusNotification::class);
});

test('adjacent events in the same hall are not conflicts', function (): void {
    $firstOwner = $this->createExhibitor();
    $secondOwner = $this->createExhibitor();
    $firstCompany = createConflictCompany('First Adjacent Company');
    $secondCompany = createConflictCompany('Second Adjacent Company');
    $firstCompany->systemUsers()->attach($firstOwner->id, ['created_at' => now()]);
    $secondCompany->systemUsers()->attach($secondOwner->id, ['created_at' => now()]);
    $eventHall = createConflictEventHall();
    $startAt = now()->addDay();

    $firstEvent = createConflictEvent($firstCompany, $eventHall, 'First adjacent event', Status::PENDING, $startAt, 1);
    $secondEvent = createConflictEvent($secondCompany, $eventHall, 'Second adjacent event', Status::PENDING, $startAt->copy()->addHour(), 1);

    $service = app(EventRequestService::class);
    $service->approve($firstEvent);
    $service->approve($secondEvent);

    $this->assertDatabaseHas('events', ['id' => $firstEvent->id, 'status' => Status::APPROVED->value]);
    $this->assertDatabaseHas('events', ['id' => $secondEvent->id, 'status' => Status::APPROVED->value]);
});

test('event booking is rejected when it overlaps an approved event in the same hall', function (): void {
    $exhibitor = $this->createExhibitor();
    $company = createConflictCompany('Existing Event Company');
    $eventHall = createConflictEventHall();
    $startAt = now()->addDay();

    createConflictEvent($company, $eventHall, 'Existing approved event', Status::APPROVED, $startAt, 2);

    $dto = new EventDTO(
        eventHallId: $eventHall->id,
        companyId: null,
        type: EventType::OTHER->value,
        title: 'Blocked overlapping event',
        description: 'This booking must not overlap an approved event.',
        start_at: $startAt->copy()->addHour()->toDateTimeString(),
        duration: 1,
        speakers: [],
        logo: null,
    );

    expect(fn () => app(EventService::class)->store($exhibitor, $dto, null))
        ->toThrow(HttpException::class, __('validation.hall_unavailable'));

    $this->assertDatabaseMissing('events', ['title' => 'Blocked overlapping event']);
});

test('approving a booth request rejects competing pending requests and notifies only their recipients', function (): void {
    $administrator = $this->createAdministrator();
    $approvedOwner = $this->createExhibitor();
    $conflictingOwner = $this->createExhibitor();
    $approvedCompany = createConflictCompany('Approved Booth Company');
    $conflictingCompany = createConflictCompany('Conflicting Booth Company');
    $approvedCompany->systemUsers()->attach($approvedOwner->id, ['created_at' => now()]);
    $conflictingCompany->systemUsers()->attach($conflictingOwner->id, ['created_at' => now()]);
    $hall = Hall::query()->create([
        'number' => 'HALL-BOOTH-CONFLICT',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-CONFLICT',
        'area' => 25,
        'price' => 500,
    ]);

    $approvedRequest = BoothRequest::query()->create([
        'booth_id' => $booth->id,
        'company_id' => $approvedCompany->id,
        'system_user_id' => $approvedOwner->id,
        'final_price' => 500,
        'status' => Status::PENDING->value,
    ]);
    $conflictingRequest = BoothRequest::query()->create([
        'booth_id' => $booth->id,
        'company_id' => $conflictingCompany->id,
        'system_user_id' => $conflictingOwner->id,
        'final_price' => 500,
        'status' => Status::PENDING->value,
    ]);

    app(BoothRequestService::class)->approve($approvedRequest);

    $this->assertDatabaseHas('booth_requests', ['id' => $approvedRequest->id, 'status' => Status::APPROVED->value]);
    $this->assertDatabaseHas('booth_requests', ['id' => $conflictingRequest->id, 'status' => Status::REJECTED->value]);

    Notification::assertSentTo(
        $conflictingOwner,
        BoothRequestStatusNotification::class,
        fn (BoothRequestStatusNotification $notification): bool => $notification->boothRequest->is($conflictingRequest)
            && $notification->status === Status::REJECTED
            && $notification->rejectionReason === RequestRejectionReason::BOOTH_CONFLICT,
    );
    Notification::assertNotSentTo($administrator, BoothRequestStatusNotification::class);
});

function createConflictCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for booking conflict tests.',
        'status' => Status::APPROVED->value,
    ]);
}

function createConflictEventHall(): EventHall
{
    return EventHall::query()->create([
        'number' => 'EVENT-HALL-'.fake()->unique()->numberBetween(1000, 9999),
        'area' => 300,
        'price_per_hour' => 100,
    ]);
}

function createConflictEvent(Company $company, EventHall $eventHall, string $title, Status $status, Carbon $startAt, int $duration): Event
{
    return $company->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => $title,
        'description' => 'An event used for schedule conflict tests.',
        'type' => EventType::OTHER->value,
        'status' => $status->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHours($duration),
        'duration' => $duration,
    ]);
}
