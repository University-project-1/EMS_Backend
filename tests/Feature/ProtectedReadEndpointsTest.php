<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Tests\Support\CreatesActors;

uses(DatabaseMigrations::class, CreatesActors::class);

function protectedReadRoutesFor(string $controllerNamespace, string $guard): Collection
{
    return collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => Str::startsWith($route->uri(), 'api/'))
        ->filter(fn (Route $route): bool => $route->parameterNames() === [])
        ->filter(fn (Route $route): bool => in_array('GET', $route->methods(), true))
        ->filter(fn (Route $route): bool => Str::startsWith($route->getActionName(), $controllerNamespace))
        ->filter(fn (Route $route): bool => collect($route->gatherMiddleware())
            ->contains("auth:{$guard}"))
        ->values();
}

function assertRoutesDoNotReturnServerErrors(object $testCase, Collection $routes): void
{
    expect($routes)->not->toBeEmpty();

    $routes->each(function (Route $route) use ($testCase): void {
        $response = $testCase->getJson('/'.$route->uri());

        expect($response->getStatusCode())
            ->toBeLessThan(500, "GET /{$route->uri()} must not return a server error");
    });
}

test('administrator read endpoints do not return server errors', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->actingAs($this->createAdministrator(), 'system');

    assertRoutesDoNotReturnServerErrors(
        $this,
        protectedReadRoutesFor('App\\Http\\Controllers\\Api\\V1\\SystemUser\\Admin\\', 'system'),
    );
});

test('exhibitor read endpoints do not return server errors', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->actingAs($this->createExhibitor(), 'system');

    assertRoutesDoNotReturnServerErrors(
        $this,
        protectedReadRoutesFor('App\\Http\\Controllers\\Api\\V1\\SystemUser\\Exhibitor\\', 'system'),
    );
});

test('visitor read endpoints do not return server errors', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->actingAs($this->createVisitor(), 'mobile');

    assertRoutesDoNotReturnServerErrors(
        $this,
        protectedReadRoutesFor('App\\Http\\Controllers\\Api\\V1\\Mobile\\', 'mobile'),
    );
});
