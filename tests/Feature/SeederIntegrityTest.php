<?php

use App\Enum\Status;
use App\Models\Booth;
use App\Models\Lead;
use App\Models\Report;
use App\Models\Review;
use App\Models\Saved;
use Database\Seeders\BoothRequestSeeder;
use Database\Seeders\BoothSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\HallSeeder;
use Database\Seeders\LeadSeeder;
use Database\Seeders\ReportSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\SavedSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('every booth assigned to a company has an approved booking request for the same company', function (): void {
    seedBoothWorkflowData($this);

    $inconsistentBoothNumbers = Booth::query()
        ->whereNotNull('company_id')
        ->whereDoesntHave('boothRequests', function ($query): void {
            $query
                ->where('status', Status::APPROVED->value)
                ->whereColumn('booth_requests.company_id', 'booths.company_id');
        })
        ->pluck('number');

    expect($inconsistentBoothNumbers)->toBeEmpty();
});

test('booth interactions target only booths with an approved booking for their assigned company', function (): void {
    seedBoothWorkflowData($this, [
        EventSeeder::class,
        SavedSeeder::class,
        ReviewSeeder::class,
        ReportSeeder::class,
        LeadSeeder::class,
    ]);

    $approvedBoothIds = approvedBoothIds();

    expect(Saved::query()
        ->where('savedable_type', Booth::class)
        ->whereNotIn('savedable_id', $approvedBoothIds)
        ->exists())->toBeFalse()
        ->and(Review::query()
            ->where('reviewable_type', Booth::class)
            ->whereNotIn('reviewable_id', $approvedBoothIds)
            ->exists())->toBeFalse()
        ->and(Report::query()
            ->where('reportable_type', Booth::class)
            ->whereNotIn('reportable_id', $approvedBoothIds)
            ->exists())->toBeFalse()
        ->and(Lead::query()
            ->where('leadable_type', Booth::class)
            ->whereNotIn('leadable_id', $approvedBoothIds)
            ->exists())->toBeFalse();
});

function seedBoothWorkflowData(object $testCase, array $additionalSeeders = []): void
{
    Storage::fake('public');

    $testCase->seed([
        UserSeeder::class,
        CompanySeeder::class,
        ServiceSeeder::class,
        HallSeeder::class,
        BoothSeeder::class,
        BoothRequestSeeder::class,
        ...$additionalSeeders,
    ]);
}

function approvedBoothIds()
{
    return Booth::query()
        ->whereNotNull('company_id')
        ->whereHas('boothRequests', function ($query): void {
            $query
                ->where('status', Status::APPROVED->value)
                ->whereColumn('booth_requests.company_id', 'booths.company_id');
        })
        ->pluck('id');
}
