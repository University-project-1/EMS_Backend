<?php

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can list booths of their assigned company only', function (): void {
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
