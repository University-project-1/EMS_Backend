<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

beforeEach(function (): void {
    $this->artisan('passport:client', [
        '--personal' => true,
        '--name' => 'EMS Test Personal Access Client',
        '--provider' => 'users',
        '--no-interaction' => true,
    ])->assertSuccessful();
});

test('visitor registration rejects an incomplete payload', function (): void {
    $this->postJson('/api/v1/auth/register')
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'password',
            'job',
            'location',
            'birthday',
            'gender',
        ]);
});

test('visitor login rejects an incomplete payload', function (): void {
    $this->postJson('/api/v1/auth/login')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['phone', 'password']);
});

test('visitor can log in with valid credentials', function (): void {
    $visitor = $this->createVisitor([
        'phone' => '+971501234567',
        'password' => Hash::make('correct-password'),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => $visitor->phone,
        'password' => 'correct-password',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user',
                'token',
            ],
        ]);
});

test('visitor login rejects an invalid password', function (): void {
    $visitor = $this->createVisitor([
        'phone' => '+971501234568',
        'password' => Hash::make('correct-password'),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => $visitor->phone,
        'password' => 'incorrect-password',
    ])->assertUnauthorized();
});

test('visitor logout requires an authenticated visitor session', function (): void {
    $this->deleteJson('/api/v1/visitor/auth/logout')->assertUnauthorized();
});
