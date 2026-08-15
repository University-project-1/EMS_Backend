<?php

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

function apiRoutes(): Collection
{
    return collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => Str::startsWith($route->uri(), 'api/'))
        ->values();
}

test('every API route resolves to a concrete controller action', function (): void {
    $controllerRoutes = apiRoutes()
        ->filter(fn (Route $route): bool => str_contains($route->getActionName(), '@'));

    expect($controllerRoutes)->not->toBeEmpty();

    $controllerRoutes->each(function (Route $route): void {
        [$controller, $method] = explode('@', $route->getActionName(), 2);

        expect(class_exists($controller))->toBeTrue();
        expect(method_exists($controller, $method))->toBeTrue();
    });
});

test('every named API route uses a stable, non-empty naming convention', function (): void {
    $names = apiRoutes()
        ->map(fn (Route $route): ?string => $route->getName())
        ->filter()
        ->values();

    $names->each(function (string $name): void {
        expect($name)->toMatch('/^[a-z0-9_.-]+$/');
    });
});

test('every protected API route without URI parameters rejects unauthenticated requests', function (): void {
    $routes = apiRoutes()
        ->filter(fn (Route $route): bool => $route->parameterNames() === [])
        ->filter(fn (Route $route): bool => collect($route->gatherMiddleware())
            ->contains(fn (string $middleware): bool => Str::startsWith($middleware, 'auth:')));

    expect($routes)->not->toBeEmpty();

    $routes->each(function (Route $route): void {
        $method = collect($route->methods())->first(fn (string $method): bool => $method !== 'HEAD');
        $response = $this->json($method, '/'.$route->uri());

        expect($response->getStatusCode())
            ->toBeIn([401, 403], "{$method} /{$route->uri()} must reject unauthenticated access");
    });
});

test('every declared API authentication guard is configured', function (): void {
    $configuredGuards = array_keys(config('auth.guards'));

    $guardsInUse = apiRoutes()
        ->flatMap(function (Route $route): array {
            return collect($route->gatherMiddleware())
                ->filter(fn (string $middleware): bool => Str::startsWith($middleware, 'auth:'))
                ->flatMap(fn (string $middleware): array => explode(',', Str::after($middleware, 'auth:')))
                ->all();
        })
        ->unique()
        ->values();

    $guardsInUse->each(function (string $guard) use ($configuredGuards): void {
        expect($configuredGuards)->toContain($guard);
    });
});
