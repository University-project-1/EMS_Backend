<?php

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can view invitations for an assigned company but not a foreign company', function (): void {
    $exhibitor = $this->createExhibitor();
    $assignedCompany = createInvitationTestCompany('Assigned Invitation Company');
    $foreignCompany = createInvitationTestCompany('Foreign Invitation Company');

    $assignedCompany->systemUsers()->attach($exhibitor->id, [
        'created_at' => now(),
    ]);

    $this->actingAs($exhibitor, 'system')
        ->getJson("/api/v1/exhibitor/companies/{$assignedCompany->id}/invitations")
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(0, 'data.data');

    $this->getJson("/api/v1/exhibitor/companies/{$foreignCompany->id}/invitations")
        ->assertForbidden();
});

function createInvitationTestCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for an exhibitor invitation authorization test.',
        'status' => Status::APPROVED->value,
    ]);
}
