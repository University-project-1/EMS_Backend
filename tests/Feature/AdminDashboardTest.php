<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('administrator can retrieve a dashboard with its stable summary structure', function (): void {
    $administrator = $this->createAdministrator();

    $this->actingAs($administrator, 'system')
        ->getJson('/api/v1/admin/dashboard?days=7')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'period',
                'summary',
                'trends',
                'breakdowns',
            ],
        ]);
});

test('administrator dashboard clamps an excessive period to thirty one days', function (): void {
    $administrator = $this->createAdministrator();

    $this->actingAs($administrator, 'system')
        ->getJson('/api/v1/admin/dashboard?days=999')
        ->assertOk()
        ->assertJsonPath('data.period.days', 31);
});
