<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

beforeEach(function (): void {
    $this->artisan('passport:client', [
        '--personal' => true,
        '--name' => 'EMS System User Test Client',
        '--provider' => 'system_users',
        '--no-interaction' => true,
    ])->assertSuccessful();
});

test('administrator login requires email and password', function (): void {
    $this->postJson('/api/v1/admin/login')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

test('exhibitor login requires email and password', function (): void {
    $this->postJson('/api/v1/exhibitor/login')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

test('administrator can log in with valid administrator credentials', function (): void {
    $administrator = $this->createAdministrator([
        'password' => Hash::make('correct-password'),
    ]);

    $this->postJson('/api/v1/admin/login', [
        'email' => $administrator->email,
        'password' => 'correct-password',
    ])
        ->assertOk()
        ->assertJsonStructure(['data' => ['user', 'token']]);
});

test('exhibitor can log in with valid exhibitor credentials', function (): void {
    $exhibitor = $this->createExhibitor([
        'password' => Hash::make('correct-password'),
    ]);

    $this->postJson('/api/v1/exhibitor/login', [
        'email' => $exhibitor->email,
        'password' => 'correct-password',
    ])
        ->assertOk()
        ->assertJsonStructure(['data' => ['user', 'token']]);
});

test('administrator login rejects exhibitor credentials', function (): void {
    $exhibitor = $this->createExhibitor([
        'password' => Hash::make('correct-password'),
    ]);

    $this->postJson('/api/v1/admin/login', [
        'email' => $exhibitor->email,
        'password' => 'correct-password',
    ])->assertUnauthorized();
});

test('exhibitor login rejects administrator credentials', function (): void {
    $administrator = $this->createAdministrator([
        'password' => Hash::make('correct-password'),
    ]);

    $this->postJson('/api/v1/exhibitor/login', [
        'email' => $administrator->email,
        'password' => 'correct-password',
    ])->assertUnauthorized();
});
