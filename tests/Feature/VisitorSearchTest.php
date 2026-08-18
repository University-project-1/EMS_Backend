<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\EventHall;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('visitor search returns matching approved companies and events only', function (): void {
    $visitor = $this->createVisitor();
    $approvedCompany = createSearchCompany('Nexus Approved Company', Status::APPROVED);
    $pendingCompany = createSearchCompany('Nexus Pending Company', Status::PENDING);
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-801',
        'area' => 150,
        'price_per_hour' => 100,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    $approvedEvent = $approvedCompany->events()->create(searchEventAttributes(
        eventHallId: $eventHall->id,
        title: 'Nexus Approved Event',
        startAt: $startAt,
        status: Status::APPROVED,
    ));
    $pendingCompany->events()->create(searchEventAttributes(
        eventHallId: $eventHall->id,
        title: 'Nexus Pending Event',
        startAt: $startAt->copy()->addDay(),
        status: Status::PENDING,
    ));

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/search?q=Nexus')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.query', 'Nexus')
        ->assertJsonCount(1, 'data.companies')
        ->assertJsonPath('data.companies.0.id', $approvedCompany->id)
        ->assertJsonCount(1, 'data.events')
        ->assertJsonPath('data.events.0.id', $approvedEvent->id);
});

test('visitor search validates a minimum query length', function (): void {
    $visitor = $this->createVisitor();

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/search?q=a')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('q');
});

function createSearchCompany(string $name, Status $status): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for visitor search tests.',
        'status' => $status->value,
    ]);
}

function searchEventAttributes(int $eventHallId, string $title, CarbonInterface $startAt, Status $status): array
{
    return [
        'event_hall_id' => $eventHallId,
        'title' => $title,
        'description' => 'An event used for visitor search tests.',
        'type' => EventType::OTHER->value,
        'status' => $status->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ];
}
