<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can view their own profile', function (): void {
    $exhibitor = $this->createExhibitor();

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/profile')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.id', $exhibitor->id)
        ->assertJsonPath('data.email', $exhibitor->email);
});

test('exhibitor can update their own profile name', function (): void {
    $exhibitor = $this->createExhibitor();

    $this->actingAs($exhibitor, 'system')
        ->postJson('/api/v1/exhibitor/profile', [
            'name' => 'Updated Exhibitor Name',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.name', 'Updated Exhibitor Name');

    $this->assertDatabaseHas('system_users', [
        'id' => $exhibitor->id,
        'name' => 'Updated Exhibitor Name',
    ]);
});
