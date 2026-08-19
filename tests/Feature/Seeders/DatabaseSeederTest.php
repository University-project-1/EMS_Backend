<?php

use App\Models\Company;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Saved;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

it('seeds leads, saved items, event images, and company galleries', function (): void {
    $this->seed(DatabaseSeeder::class);

    $companies = Company::query()->with('media')->get();
    $events = Event::query()->with('media')->get();
    $companiesWithGallery = $companies->filter(
        fn (Company $company): bool => $company->getMedia('gallery')->count() >= 2,
    );
    $eventsWithLogo = $events->filter(
        fn (Event $event): bool => $event->getMedia('event-logo')->count() >= 1,
    );

    expect($companies->count())->toBeGreaterThanOrEqual(11)
        ->and($events->count())->toBeGreaterThanOrEqual(5)
        ->and($companiesWithGallery->count())->toBeGreaterThanOrEqual(11)
        ->and($eventsWithLogo->count())->toBeGreaterThanOrEqual(5)
        ->and(Lead::query()->count())->toBeGreaterThan(0)
        ->and(Saved::query()->count())->toBeGreaterThan(0);
});
