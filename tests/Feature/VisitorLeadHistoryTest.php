<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('an authenticated visitor can read an empty lead history', function (): void {
    $visitor = $this->createVisitor();

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/leads/history')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data', []);
});
