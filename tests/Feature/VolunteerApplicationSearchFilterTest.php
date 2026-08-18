<?php

use App\Filter\VolunteerApplicationSearchFilter;
use App\Models\VolunteerApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('searches volunteer applications by name email or phone', function (): void {
    $nameMatch = VolunteerApplication::factory()->create([
        'full_name' => 'نور الهدى',
        'email' => 'noor@example.test',
        'phone' => '+963944100001',
    ]);
    $emailMatch = VolunteerApplication::factory()->create([
        'full_name' => 'متقدم آخر',
        'email' => 'applicant.search@example.test',
        'phone' => '+963944100002',
    ]);
    $phoneMatch = VolunteerApplication::factory()->create([
        'full_name' => 'متقدم ثالث',
        'email' => 'third@example.test',
        'phone' => '+963944100003',
    ]);

    $filter = new VolunteerApplicationSearchFilter;

    $byName = VolunteerApplication::query();
    $filter($byName, 'نور', 'search');

    $byEmail = VolunteerApplication::query();
    $filter($byEmail, 'applicant.search', 'search');

    $byPhone = VolunteerApplication::query();
    $filter($byPhone, '100003', 'search');

    expect($byName->pluck('id')->all())->toBe([$nameMatch->id]);
    expect($byEmail->pluck('id')->all())->toBe([$emailMatch->id]);
    expect($byPhone->pluck('id')->all())->toBe([$phoneMatch->id]);
});
