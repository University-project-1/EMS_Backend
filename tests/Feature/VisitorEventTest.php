<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\EventHall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('visitor event list includes approved events and excludes pending events', function (): void {
    $visitor = $this->createVisitor();
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-201',
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $company = createVisitorEventCompany();

    $approvedEvent = $company->events()->create(visitorEventAttributes(
        eventHallId: $eventHall->id,
        title: 'Approved visitor event',
        status: Status::APPROVED,
    ));
    $company->events()->create(visitorEventAttributes(
        eventHallId: $eventHall->id,
        title: 'Pending visitor event',
        status: Status::PENDING,
    ));

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/events')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.id', $approvedEvent->id)
        ->assertJsonPath('data.data.0.title', 'Approved visitor event');
});

function createVisitorEventCompany(): Company
{
    return Company::query()->create([
        'name' => 'Visitor Event Company',
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for a visitor event test.',
        'status' => Status::APPROVED->value,
    ]);
}

function visitorEventAttributes(int $eventHallId, string $title, Status $status): array
{
    $startAt = now()->addDays(3)->startOfHour();

    return [
        'event_hall_id' => $eventHallId,
        'title' => $title,
        'description' => 'Event description for testing.',
        'type' => EventType::OTHER->value,
        'status' => $status->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ];
}
