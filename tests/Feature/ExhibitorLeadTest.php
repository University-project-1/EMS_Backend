<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can view leads for an owned event but not another company event', function (): void {
    $exhibitor = $this->createExhibitor();
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-301',
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $ownedCompany = createLeadTestCompany('Owned Lead Company');
    $foreignCompany = createLeadTestCompany('Foreign Lead Company');

    $ownedCompany->systemUsers()->attach($exhibitor->id, [
        'created_at' => now(),
    ]);

    $ownedEvent = createLeadTestEvent($ownedCompany, $eventHall->id, 'Owned lead event');
    $foreignEvent = createLeadTestEvent($foreignCompany, $eventHall->id, 'Foreign lead event');

    $this->actingAs($exhibitor, 'system')
        ->getJson("/api/v1/exhibitor/leads/events/{$ownedEvent->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->getJson("/api/v1/exhibitor/leads/events/{$foreignEvent->id}")
        ->assertForbidden();
});

function createLeadTestCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for an exhibitor lead authorization test.',
        'status' => Status::APPROVED->value,
    ]);
}

function createLeadTestEvent(Company $company, int $eventHallId, string $title): Event
{
    $startAt = now()->addDays(3)->startOfHour();

    return $company->events()->create([
        'event_hall_id' => $eventHallId,
        'title' => $title,
        'description' => 'Event description for testing.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}
