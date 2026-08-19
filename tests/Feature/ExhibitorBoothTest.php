<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\EventHall;
use App\Models\Hall;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can list booths with an approved booking request only', function (): void {
    $exhibitor = $this->createExhibitor();
    $hall = Hall::query()->create([
        'number' => 'HALL-601',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $assignedCompany = createExhibitorBoothCompany('Assigned Booth Company');
    $foreignCompany = createExhibitorBoothCompany('Foreign Booth Company');

    $assignedCompany->systemUsers()->attach($exhibitor->id, [
        'created_at' => now(),
    ]);

    $ownedBooth = $hall->booths()->create([
        'company_id' => $assignedCompany->id,
        'number' => 'A-101',
        'area' => 25,
        'price' => 500,
    ]);
    $ownedBooth->boothRequests()->create([
        'company_id' => $assignedCompany->id,
        'system_user_id' => $exhibitor->id,
        'final_price' => 500,
        'status' => Status::APPROVED->value,
        'reason_for_booking' => 'A confirmed booking for the exhibitor.',
    ]);

    $hall->booths()->create([
        'company_id' => $foreignCompany->id,
        'number' => 'A-102',
        'area' => 25,
        'price' => 500,
    ]);

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/booth/my')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.id', $ownedBooth->id)
        ->assertJsonPath('data.data.0.number', 'A-101');
});

test('exhibitor can retrieve aggregate statistics for an accessible booth', function (): void {
    $exhibitor = $this->createExhibitor();
    $otherBoothMember = $this->createExhibitor();
    $visitorOne = $this->createVisitor();
    $visitorTwo = $this->createVisitor();
    $visitorThree = $this->createVisitor();
    $hall = Hall::query()->create([
        'number' => 'HALL-602',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $eventHall = EventHall::query()->create([
        'number' => 'EVENT-HALL-602',
        'area' => 150,
        'price_per_hour' => 100,
    ]);
    $company = createExhibitorBoothCompany('Statistics Booth Company');
    $company->systemUsers()->attach($exhibitor->id, ['created_at' => now()]);

    $booth = $hall->booths()->create([
        'company_id' => $company->id,
        'number' => 'A-201',
        'area' => 25,
        'price' => 500,
    ]);
    $booth->systemUsers()->attach([
        $exhibitor->id => ['created_at' => now()],
        $otherBoothMember->id => ['created_at' => now()],
    ]);

    $boothRequest = $booth->boothRequests()->create([
        'company_id' => $company->id,
        'system_user_id' => $exhibitor->id,
        'final_price' => 535,
        'status' => Status::APPROVED->value,
        'reason_for_booking' => 'Approved booking for statistics.',
    ]);
    $firstService = Service::query()->create([
        'name' => 'Statistics service one',
        'price' => 10,
    ]);
    $secondService = Service::query()->create([
        'name' => 'Statistics service two',
        'price' => 5,
    ]);
    $boothRequest->services()->createMany([
        ['service_id' => $firstService->id, 'quantity' => 2, 'unit_price' => 10],
        ['service_id' => $secondService->id, 'quantity' => 3, 'unit_price' => 5],
    ]);

    $oldLead = $booth->leads()->create(['user_id' => $visitorOne->id]);
    $oldLead->forceFill(['created_at' => now()->subDays(31)])->save();
    $booth->leads()->create(['user_id' => $visitorTwo->id]);
    $booth->leads()->create(['user_id' => $visitorThree->id, 'created_at' => now()]);
    $booth->invitations()->create([
        'sender_id' => $exhibitor->id,
        'email' => 'pending-statistics@example.com',
        'token' => 'pending-statistics-token',
        'status' => Status::PENDING->value,
        'expires_at' => now()->addDay(),
    ]);
    $booth->invitations()->create([
        'sender_id' => $exhibitor->id,
        'email' => 'approved-statistics@example.com',
        'token' => 'approved-statistics-token',
        'status' => Status::APPROVED->value,
        'expires_at' => now()->addDay(),
    ]);

    $eventAttributes = [
        'event_hall_id' => $eventHall->id,
        'title' => 'Direct exhibitor event',
        'description' => 'An event created by the exhibitor.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => now()->addDay(),
        'end_at' => now()->addDay()->addHour(),
        'duration' => 60,
    ];
    $exhibitor->events()->create($eventAttributes);
    $company->events()->create([
        ...$eventAttributes,
        'title' => 'Company pending event',
        'status' => Status::PENDING->value,
    ]);

    $this->actingAs($exhibitor, 'system')
        ->getJson("/api/v1/exhibitor/booth/{$booth->id}/statistics")
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.leads_count', 3)
        ->assertJsonPath('data.recent_qr_scans_count', 2)
        ->assertJsonPath('data.services_count', 2)
        ->assertJsonPath('data.services_total_price', 35)
        ->assertJsonPath('data.booth_members_count', 2)
        ->assertJsonPath('data.pending_invitations_count', 1)
        ->assertJsonPath('data.events_count', 2)
        ->assertJsonPath('data.approved_events_count', 1);
});

function createExhibitorBoothCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for an exhibitor booth test.',
        'status' => Status::APPROVED->value,
    ]);
}
