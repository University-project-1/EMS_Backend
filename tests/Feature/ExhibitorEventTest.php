<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\EventHall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can list events belonging to their assigned company only', function (): void {
    $exhibitor = $this->createExhibitor();
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-101',
        'area' => 120,
        'price_per_hour' => 250,
    ]);

    $assignedCompany = createCompany('Assigned Company');
    $foreignCompany = createCompany('Foreign Company');

    $assignedCompany->systemUsers()->attach($exhibitor->id, [
        'created_at' => now(),
    ]);

    $ownEvent = $assignedCompany->events()->create(eventAttributes($eventHall->id, 'Assigned event'));
    $foreignCompany->events()->create(eventAttributes($eventHall->id, 'Foreign event'));

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/events')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.id', $ownEvent->id)
        ->assertJsonPath('data.data.0.title', 'Assigned event');
});

function createCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for an exhibitor event test.',
        'status' => Status::APPROVED->value,
    ]);
}

function eventAttributes(int $eventHallId, string $title): array
{
    $startAt = now()->addDays(3)->startOfHour();

    return [
        'event_hall_id' => $eventHallId,
        'title' => $title,
        'description' => 'Event description for testing.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ];
}
