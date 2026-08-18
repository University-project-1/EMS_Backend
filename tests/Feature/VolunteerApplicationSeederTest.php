<?php

use App\Enum\Status;
use App\Models\VolunteerApplication;
use Database\Seeders\VolunteerApplicationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds a balanced and idempotent set of volunteer applications', function (): void {
    $this->seed(VolunteerApplicationSeeder::class);

    expect(VolunteerApplication::query()->count())->toBe(48);
    expect(VolunteerApplication::query()->where('status', Status::PENDING)->count())->toBe(24);
    expect(VolunteerApplication::query()->where('status', Status::APPROVED)->count())->toBe(14);
    expect(VolunteerApplication::query()->where('status', Status::REJECTED)->count())->toBe(10);

    $this->seed(VolunteerApplicationSeeder::class);

    expect(VolunteerApplication::query()->count())->toBe(48);
});
