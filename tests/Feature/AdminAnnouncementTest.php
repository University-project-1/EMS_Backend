<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('administrator can create an announcement', function (): void {
    Notification::fake();
    $administrator = $this->createAdministrator();

    $this->actingAs($administrator, 'system')
        ->postJson('/api/v1/admin/announcements', [
            'title' => 'Platform maintenance',
            'description' => 'The platform will be unavailable for scheduled maintenance.',
            'receiver' => 'all',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.title', 'Platform maintenance');

    $this->assertDatabaseHas('announcements', [
        'title' => 'Platform maintenance',
        'receiver' => 'all',
    ]);
});

test('administrator cannot create an announcement without a title', function (): void {
    $administrator = $this->createAdministrator();

    $this->actingAs($administrator, 'system')
        ->postJson('/api/v1/admin/announcements', [
            'description' => 'The title is intentionally missing.',
            'receiver' => 'all',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});
