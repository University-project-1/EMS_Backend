<?php

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('admin can cancel an approved booth booking and release the booth', function (): void {
    $admin = $this->createAdministrator();
    $company = cancellationCompany();
    $hall = Hall::query()->create([
        'number' => 'HALL-CANCEL-701',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-CANCEL-701',
        'area' => 25,
        'price' => 500,
        'company_id' => $company->id,
        'qr_token' => 'B-'.$company->id.'-old-token',
    ]);
    $request = BoothRequest::query()->create([
        'booth_id' => $booth->id,
        'company_id' => $company->id,
        'system_user_id' => $admin->id,
        'final_price' => 500,
        'status' => Status::APPROVED,
    ]);

    $this->actingAs($admin, 'system')
        ->patchJson("/api/v1/admin/booths/{$booth->id}/cancel")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('booth_requests', [
        'id' => $request->id,
        'status' => Status::CANCELED->value,
    ]);
    $this->assertDatabaseHas('booths', [
        'id' => $booth->id,
        'company_id' => null,
        'qr_token' => null,
    ]);
});

test('admin cannot cancel a non-approved booth request', function (): void {
    $admin = $this->createAdministrator();
    $company = cancellationCompany('Invalid Cancellation Company');
    $hall = Hall::query()->create([
        'number' => 'HALL-CANCEL-702',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-CANCEL-702',
        'area' => 25,
        'price' => 500,
    ]);
    $request = BoothRequest::query()->create([
        'booth_id' => $booth->id,
        'company_id' => $company->id,
        'system_user_id' => $admin->id,
        'final_price' => 500,
        'status' => Status::PENDING,
    ]);

    $this->actingAs($admin, 'system')
        ->patchJson("/api/v1/admin/booths/{$booth->id}/cancel")
        ->assertStatus(400);

    $this->assertDatabaseHas('booth_requests', [
        'id' => $request->id,
        'status' => Status::PENDING->value,
    ]);
});

function cancellationCompany(string $name = 'Cancellation Company'): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for booth cancellation tests.',
        'status' => Status::APPROVED->value,
    ]);
}
