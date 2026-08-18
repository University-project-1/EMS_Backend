<?php

use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

test('sensitive endpoints and the API group use the intended rate limiters', function (): void {
    $expectedMiddleware = [
        'api/v1/visitor/search' => 'throttle:api',
        'api/v1/auth/register' => 'throttle:registration',
        'api/v1/auth/login' => 'throttle:mobile_login',
        'api/v1/auth/register/verify' => 'throttle:verify_otp',
        'api/v1/admin/login' => 'throttle:system_login',
        'api/v1/exhibitor/login' => 'throttle:system_login',
        'api/v1/exhibitor/booth/request-booth' => 'throttle:booth_request',
        'api/v1/visitor/reviews' => 'throttle:review',
        'api/v1/visitor/leads' => 'throttle:lead',
    ];

    foreach ($expectedMiddleware as $uri => $middleware) {
        $route = collect(app('router')->getRoutes())
            ->first(fn ($route) => $route->uri() === $uri);

        expect($route)
            ->not->toBeNull()
            ->and($route->gatherMiddleware())
            ->toContain($middleware);
    }
});

test('mobile registration is rejected after three attempts with a translated response', function (): void {
    $payload = [
        'phone' => '+963991123456',
    ];

    foreach (range(1, 3) as $attempt) {
        $this->postJson('/api/v1/auth/register', $payload);
    }

    $this->postJson('/api/v1/auth/register', $payload)
        ->assertTooManyRequests()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', __('rate_limit.registration'));
});

test('system login is rejected after five attempts with a translated response', function (): void {
    $payload = [
        'email' => 'throttle-admin@example.test',
        'password' => 'incorrect-password',
    ];

    foreach (range(1, 5) as $attempt) {
        $this->postJson('/api/v1/admin/login', $payload);
    }

    $this->postJson('/api/v1/admin/login', $payload)
        ->assertTooManyRequests()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', __('rate_limit.system_login'));
});

test('password recovery is rejected after three requests within fifteen minutes', function (): void {
    $payload = [
        'phone' => '+963991654321',
    ];

    foreach (range(1, 3) as $attempt) {
        $this->postJson('/api/v1/auth/password/forgot', $payload);
    }

    $this->postJson('/api/v1/auth/password/forgot', $payload)
        ->assertTooManyRequests()
        ->assertJsonPath('status', false)
        ->assertJsonPath('message', __('rate_limit.forgot_password'));
});
