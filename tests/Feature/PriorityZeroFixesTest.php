<?php

use App\Enum\DeviceType;
use App\Enum\SystemUserType;
use App\Http\Controllers\Api\V1\SystemUser\Admin\ServiceController;
use App\Http\Controllers\Api\V1\SystemUser\Exhibitor\LeadController;
use App\Models\Event;
use App\Models\SystemUser;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route as RouteFacade;
use Laravel\Passport\AccessToken;

uses(LazilyRefreshDatabase::class);

test('all authenticated exhibitor routes enforce the exhibitor role', function () {
    $authenticatedExhibitorRoutes = collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => str_starts_with($route->uri(), 'api/v1/exhibitor/')
            && in_array('auth:system', $route->gatherMiddleware(), true));

    expect($authenticatedExhibitorRoutes)->not->toBeEmpty();

    $authenticatedExhibitorRoutes->each(function (Route $route): void {
        expect($route->gatherMiddleware())
            ->toContain('type.exhibitor');
    });
});

test('an administrator cannot use an authenticated exhibitor endpoint', function () {
    $administrator = SystemUser::query()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.test',
        'password' => 'password',
        'type' => SystemUserType::ADMIN,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($administrator, 'system')
        ->getJson('/api/v1/exhibitor/profile')
        ->assertForbidden();
});

test('an exhibitor can use an authenticated exhibitor endpoint', function () {
    $exhibitor = SystemUser::query()->create([
        'name' => 'Exhibitor User',
        'email' => 'exhibitor@example.test',
        'password' => 'password',
        'type' => SystemUserType::EXHIBITOR,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/profile')
        ->assertOk();
});

test('system FCM registration stores the current system access token id', function () {
    $accessTokenId = 'system-access-token';
    $administrator = SystemUser::query()->create([
        'name' => 'Admin User',
        'email' => 'fcm-admin@example.test',
        'password' => 'password',
        'type' => SystemUserType::ADMIN,
        'email_verified_at' => now(),
    ])->withAccessToken(new AccessToken([
        'oauth_access_token_id' => $accessTokenId,
        'oauth_scopes' => [],
    ]));

    $this->actingAs($administrator, 'system')
        ->postJson('/api/v1/admin/fcm/register-token', [
            'token' => 'system-fcm-token',
            'device_type' => DeviceType::WEB->value,
        ])
        ->assertOk();

    $deviceToken = $administrator->deviceTokens()
        ->where('fcm_token', 'system-fcm-token')
        ->firstOrFail();

    expect($deviceToken->oauth_access_token_id)->toBe($accessTokenId);
});

test('a system user can change their password successfully', function () {
    $administrator = SystemUser::query()->create([
        'name' => 'Password Admin',
        'email' => 'password-admin@example.test',
        'password' => 'current-password',
        'type' => SystemUserType::ADMIN,
        'email_verified_at' => now(),
    ])->withAccessToken(new AccessToken([
        'id' => 'password-access-token',
        'oauth_access_token_id' => 'password-access-token',
        'oauth_scopes' => [],
    ]));

    $this->actingAs($administrator, 'system')
        ->postJson('/api/v1/admin/change-password', [
            'current_password' => 'current-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])
        ->assertOk();

    expect(Hash::check('new-password', $administrator->fresh()->password))->toBeTrue();
});

test('event lead binding uses the application event model', function () {
    $eventParameterType = (new ReflectionMethod(LeadController::class, 'eventLeads'))
        ->getParameters()[0]
        ->getType();

    expect($eventParameterType)
        ->toBeInstanceOf(ReflectionNamedType::class)
        ->and($eventParameterType->getName())
        ->toBe(Event::class);
});

test('admin service routes expose only the actions implemented by the controller', function () {
    $serviceActions = collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => str_starts_with(
            $route->getActionName(),
            ServiceController::class.'@',
        ))
        ->map(fn (Route $route): string => $route->getActionMethod())
        ->sort()
        ->values()
        ->all();

    expect($serviceActions)->toBe(['index', 'show', 'store', 'update']);
});

test('all controller routes target methods that exist', function () {
    collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => str_contains($route->getActionName(), '@'))
        ->each(function (Route $route): void {
            [$controller, $method] = explode('@', $route->getActionName(), 2);

            expect(method_exists($controller, $method))
                ->toBeTrue("{$route->methods()[0]} {$route->uri()} targets missing method {$route->getActionName()}");
        });
});
