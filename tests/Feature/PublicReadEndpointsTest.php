<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

uses(DatabaseMigrations::class);

function publicApiReadRoutes(): Collection
{
    return collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => Str::startsWith($route->uri(), 'api/'))
        ->filter(fn (Route $route): bool => $route->parameterNames() === [])
        ->filter(fn (Route $route): bool => in_array('GET', $route->methods(), true))
        ->reject(fn (Route $route): bool => collect($route->gatherMiddleware())
            ->contains(fn (string $middleware): bool => Str::startsWith($middleware, 'auth:')))
        ->values();
}

test('every public API collection endpoint is readable after database seeding', function (): void {
    $this->seed(DatabaseSeeder::class);

    $routes = publicApiReadRoutes();

    expect($routes)->not->toBeEmpty();

    $routes->each(function (Route $route): void {
        $this->getJson('/'.$route->uri())
            ->assertOk();
    });
});
