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

    expect($companies)->toHaveCount(11)
        ->and($events)->toHaveCount(5)
        ->and(Lead::query()->count())->toBeGreaterThan(0)
        ->and(Saved::query()->count())->toBeGreaterThan(0);

    $companies->each(function (Company $company): void {
        expect($company->getMedia('gallery'))->toHaveCount(2);
    });

    $events->each(function (Event $event): void {
        expect($event->getMedia('event-logo'))->toHaveCount(1);
    });
});
