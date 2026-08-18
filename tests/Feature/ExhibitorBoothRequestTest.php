<?php

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;
use Tests\Support\CreatesProductCatalog;

uses(RefreshDatabase::class, CreatesActors::class, CreatesProductCatalog::class);

test('exhibitor can submit a booth booking request for an assigned company', function (): void {
    $exhibitor = $this->createExhibitor();
    $company = createExhibitorBookingCompany('Assigned Booking Company');
    $company->systemUsers()->attach($exhibitor->id, ['created_at' => now()]);

    $hall = Hall::query()->create([
        'number' => 'HALL-701',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-701',
        'area' => 25,
        'price' => 500,
    ]);

    $this->actingAs($exhibitor, 'system')
        ->post('/api/v1/exhibitor/booth/request-booth', [
            'booth_id' => $booth->id,
            'company_id' => $company->id,
            'reason_for_booking' => 'We need a booth for our product demonstration.',
            'products_file' => $this->createProductCatalogFile([
                ['name', 'price', 'description'],
                ['Product Demonstration Kit', '1250.00', 'Products presented at the exhibition booth.'],
            ]),
        ], [
            'Accept' => 'application/json',
        ])
        ->assertSuccessful()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('booth_requests', [
        'booth_id' => $booth->id,
        'company_id' => $company->id,
        'system_user_id' => $exhibitor->id,
        'status' => Status::PENDING->value,
    ]);

    $boothRequest = BoothRequest::query()->sole();

    $this->assertDatabaseHas('booth_products', [
        'booth_request_id' => $boothRequest->id,
        'name' => 'Product Demonstration Kit',
        'price' => '1250.00',
    ]);
});

test('exhibitor cannot submit a booth booking request for another company', function (): void {
    $exhibitor = $this->createExhibitor();
    $foreignCompany = createExhibitorBookingCompany('Foreign Booking Company');

    $hall = Hall::query()->create([
        'number' => 'HALL-702',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-702',
        'area' => 25,
        'price' => 500,
    ]);

    $this->actingAs($exhibitor, 'system')
        ->postJson('/api/v1/exhibitor/booth/request-booth', [
            'booth_id' => $booth->id,
            'company_id' => $foreignCompany->id,
            'reason_for_booking' => 'An unauthorized booking request.',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('booth_requests', 0);
});

test('exhibitor sees only their own booth requests and can filter by status', function (): void {
    $exhibitor = $this->createExhibitor();
    $foreignExhibitor = $this->createExhibitor();
    $company = createExhibitorBookingCompany('Visible Booking Company');
    $foreignCompany = createExhibitorBookingCompany('Hidden Booking Company');

    $hall = Hall::query()->create([
        'number' => 'HALL-703',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $visibleBooth = $hall->booths()->create([
        'number' => 'B-703',
        'area' => 25,
        'price' => 500,
    ]);
    $hiddenBooth = $hall->booths()->create([
        'number' => 'B-704',
        'area' => 25,
        'price' => 500,
    ]);

    $visibleRequest = BoothRequest::query()->create([
        'booth_id' => $visibleBooth->id,
        'company_id' => $company->id,
        'system_user_id' => $exhibitor->id,
        'final_price' => 500,
        'status' => Status::PENDING->value,
    ]);
    BoothRequest::query()->create([
        'booth_id' => $hiddenBooth->id,
        'company_id' => $foreignCompany->id,
        'system_user_id' => $foreignExhibitor->id,
        'final_price' => 500,
        'status' => Status::PENDING->value,
    ]);

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/booth-requests?filter[status]=pending')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.data.0.id', $visibleRequest->id);
});

function createExhibitorBookingCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for exhibitor booking request tests.',
        'status' => Status::APPROVED->value,
    ]);
}
