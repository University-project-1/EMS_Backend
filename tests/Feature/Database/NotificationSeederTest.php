<?php

use App\Enum\SystemUserType;
use App\Models\SystemUser;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds the complete notification catalog for each recipient role', function (): void {
    /** @phpstan-ignore-next-line Laravel Pest binds the test case at runtime. */
    $this->seed(DatabaseSeeder::class);

    $admin = SystemUser::query()
        ->where('type', SystemUserType::ADMIN)
        ->firstOrFail();
    $exhibitor = SystemUser::query()
        ->where('type', SystemUserType::EXHIBITOR)
        ->firstOrFail();
    $visitor = User::query()->firstOrFail();

    $adminTypes = $admin->notifications()
        ->get()
        ->map(fn ($notification): string => $notification->data['type'])
        ->all();
    $exhibitorTypes = $exhibitor->notifications()
        ->get()
        ->map(fn ($notification): string => $notification->data['type'])
        ->all();
    $visitorTypes = $visitor->notifications()
        ->get()
        ->map(fn ($notification): string => $notification->data['type'])
        ->all();

    expect($adminTypes)->toContain(
        'report_created',
        'event_booking_request_created',
        'booth_booking_request_created',
    )->and($exhibitorTypes)->toContain(
        'event_approved',
        'event_rejected',
        'booth_approved',
        'booth_rejected',
        'review_created',
        'announcement',
    )->and($visitorTypes)->toContain(
        'announcement',
        'event_reminder',
        'company_booth_created',
        'company_event_created',
        'organizer_event_created',
    );
});
