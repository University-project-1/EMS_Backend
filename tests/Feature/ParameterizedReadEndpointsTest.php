<?php

use App\Models\Announcement;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\BusCatalog;
use App\Models\Company;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Hall;
use App\Models\Report;
use App\Models\Review;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Tests\Support\CreatesActors;

uses(DatabaseMigrations::class, CreatesActors::class);

function parameterizedProtectedReadRoutes(): Collection
{
    return collect(RouteFacade::getRoutes())
        ->filter(fn (Route $route): bool => Str::startsWith($route->uri(), 'api/'))
        ->filter(fn (Route $route): bool => $route->parameterNames() !== [])
        ->filter(fn (Route $route): bool => in_array('GET', $route->methods(), true))
        ->filter(fn (Route $route): bool => collect($route->gatherMiddleware())
            ->contains(fn (string $middleware): bool => Str::startsWith($middleware, 'auth:')))
        ->values();
}

function seededRouteParameterValue(string $parameter): int
{
    return match ($parameter) {
        'announcement' => Announcement::query()->value('id') ?? 1,
        'booth' => Booth::query()->value('id') ?? 1,
        'boothRequest', 'booth_request' => BoothRequest::query()->value('id') ?? 1,
        'bus', 'busCatalog', 'bus_catalog' => BusCatalog::query()->value('id') ?? 1,
        'company' => Company::query()->value('id') ?? 1,
        'event' => Event::query()->value('id') ?? 1,
        'facility' => Facility::query()->value('id') ?? 1,
        'hall' => Hall::query()->value('id') ?? 1,
        'report' => Report::query()->value('id') ?? 1,
        'review' => Review::query()->value('id') ?? 1,
        'service' => Service::query()->value('id') ?? 1,
        default => 1,
    };
}

function uriWithSeededParameters(Route $route): string
{
    return preg_replace_callback(
        '/\{([^}?]+)\??\}/',
        fn (array $matches): int => seededRouteParameterValue($matches[1]),
        $route->uri(),
    );
}

test('every protected parameterized read endpoint avoids server errors', function (): void {
    $this->seed(DatabaseSeeder::class);

    $administrator = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $visitor = $this->createVisitor();
    $routes = parameterizedProtectedReadRoutes();

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

        $response = $this->getJson('/'.uriWithSeededParameters($route));

        expect($response->getStatusCode())
            ->toBeLessThan(500, "GET /{$route->uri()} must not return a server error");
    });
});
