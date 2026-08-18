<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Tests\Support\CreatesActors;

uses(DatabaseMigrations::class, CreatesActors::class);

function protectedWriteRoutes(): Collection
{
    return collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => Str::startsWith($route->uri(), 'api/'))
        ->filter(fn (Route $route): bool => $route->parameterNames() === [])
        ->filter(fn (Route $route): bool => collect($route->methods())
            ->contains(fn (string $method): bool => in_array($method, ['POST', 'PUT', 'PATCH'], true)))
        ->filter(fn (Route $route): bool => collect($route->gatherMiddleware())
            ->contains(fn (string $middleware): bool => Str::startsWith($middleware, 'auth:')))
        ->reject(fn (Route $route): bool => Str::endsWith($route->uri(), 'logout'))
        ->values();
}

function writeMethod(Route $route): string
{
    return collect($route->methods())
        ->first(fn (string $method): bool => in_array($method, ['POST', 'PUT', 'PATCH'], true));
}

test('every protected parameterless write endpoint handles an empty payload without server errors', function (): void {
    $this->seed(DatabaseSeeder::class);

    $administrator = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $visitor = $this->createVisitor();
    $routes = protectedWriteRoutes();

    expect($routes)->not->toBeEmpty();

    $routes->each(function (Route $route) use ($administrator, $exhibitor, $visitor): void {
        $middleware = collect($route->gatherMiddleware());

        if ($middleware->contains('auth:mobile')) {
            $this->actingAs($visitor, 'mobile');
        } elseif (Str::contains($route->getActionName(), '\\SystemUser\\Exhibitor\\')) {
            $this->actingAs($exhibitor, 'system');
        } else {
            $this->actingAs($administrator, 'system');
        }

        $response = $this->json(writeMethod($route), '/'.$route->uri());

        expect($response->getStatusCode())
            ->toBeLessThan(500, "{$route->methods()[0]} /{$route->uri()} must not return a server error");
    });
});
