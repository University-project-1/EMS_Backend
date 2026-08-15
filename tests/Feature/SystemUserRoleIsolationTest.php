<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Tests\Support\CreatesActors;

uses(LazilyRefreshDatabase::class, CreatesActors::class);

function protectedSystemRoutesFor(string $controllerNamespace): Collection
{
    return collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => Str::startsWith($route->uri(), 'api/'))
        ->filter(fn (Route $route): bool => $route->parameterNames() === [])
        ->filter(fn (Route $route): bool => Str::startsWith($route->getActionName(), $controllerNamespace))
        ->filter(fn (Route $route): bool => collect($route->gatherMiddleware())
            ->contains(fn (string $middleware): bool => Str::startsWith($middleware, 'auth:system')))
        ->values();
}

function firstNonHeadMethod(Route $route): string
{
    return collect($route->methods())->first(fn (string $method): bool => $method !== 'HEAD');
}

test('every protected admin route rejects an exhibitor account', function (): void {
    $routes = protectedSystemRoutesFor('App\\Http\\Controllers\\Api\\V1\\SystemUser\\Admin\\');
    $exhibitor = $this->createExhibitor();

    expect($routes)->not->toBeEmpty();

    $this->actingAs($exhibitor, 'system');

    $routes->each(function (Route $route): void {
        $method = firstNonHeadMethod($route);

        $this->json($method, '/'.$route->uri())
            ->assertForbidden();
    });
});

test('every protected exhibitor route rejects an administrator account', function (): void {
    $routes = protectedSystemRoutesFor('App\\Http\\Controllers\\Api\\V1\\SystemUser\\Exhibitor\\');
    $administrator = $this->createAdministrator();

    expect($routes)->not->toBeEmpty();

    $this->actingAs($administrator, 'system');

    $routes->each(function (Route $route): void {
        $method = firstNonHeadMethod($route);

        $this->json($method, '/'.$route->uri())
            ->assertForbidden();
    });
});
