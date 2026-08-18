<?php

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can view a company profile', function (): void {
    $exhibitor = $this->createExhibitor();
    $company = createExhibitorCompany('Company Profile Test');

    $this->actingAs($exhibitor, 'system')
        ->getJson("/api/v1/exhibitor/companies/{$company->id}/profile")
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.company.id', $company->id)
        ->assertJsonPath('data.company.name', 'Company Profile Test')
        ->assertJsonPath('data.exhibitor.id', $exhibitor->id);
});

test('exhibitor receives not found for a missing company profile', function (): void {
    $exhibitor = $this->createExhibitor();

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/companies/999999/profile')
        ->assertNotFound();
});

function createExhibitorCompany(string $name): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for an exhibitor company profile test.',
        'status' => Status::APPROVED->value,
    ]);
}
