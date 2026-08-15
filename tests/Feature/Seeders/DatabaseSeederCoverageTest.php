<?php

use App\Models\Announcement;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\BusCatalog;
use App\Models\Company;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Hall;
use App\Models\Lead;
use App\Models\Report;
use App\Models\Review;
use App\Models\Saved;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;

uses(DatabaseMigrations::class);

test('database seeder provides all baseline catalog and workflow data', function (): void {
    $this->seed(DatabaseSeeder::class);

    $models = [
        Announcement::class,
        Booth::class,
        BoothRequest::class,
        BusCatalog::class,
        Company::class,
        Event::class,
        Facility::class,
        Hall::class,
        Lead::class,
        Report::class,
        Review::class,
        Saved::class,
        Service::class,
        User::class,
    ];

    foreach ($models as $model) {
        expect($model::query()->count())
            ->toBeGreaterThan(0, "{$model} must contain baseline seeded records");
    }

    expect(DB::table('oauth_clients')->count())
        ->toBeGreaterThan(0, 'PassportSeeder must create at least one OAuth client');
});

test('database seeder leaves every saved item attached to a valid visitor and resource', function (): void {
    $this->seed(DatabaseSeeder::class);

    Saved::query()->each(function (Saved $saved): void {
        expect($saved->user()->exists())->toBeTrue();
        expect($saved->savedable()->exists())->toBeTrue();
    });
});
