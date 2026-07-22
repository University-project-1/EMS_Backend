<?php

use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\EventHall;
use App\Models\Facility;
use App\Models\Hall;
use Database\Seeders\BoothRequestSeeder;
use Database\Seeders\BoothSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\FacilitySeeder;
use Database\Seeders\HallSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('map venue seeders preserve the flat hall structure', function () {
    $legacyHall = Hall::query()->create([
        'number' => 'Hall-A',
        'area' => 500.0,
        'type' => 'exhibition',
    ]);
    Booth::query()->create([
        'hall_id' => $legacyHall->id,
        'number' => 'A-01',
        'area' => 9.0,
        'price' => 250.0,
    ]);
    EventHall::query()->create([
        'number' => '1',
        'area' => 100.0,
        'price_per_hour' => 50000.0,
    ]);

    (new HallSeeder)->run();
    (new EventSeeder)->run();
    (new FacilitySeeder)->run();
    (new HallSeeder)->run();
    (new EventSeeder)->run();
    (new FacilitySeeder)->run();

    expect(Hall::query()->count())->toBe(22)
        ->and(EventHall::query()->count())->toBe(13)
        ->and(Facility::query()->count())->toBe(24)
        ->and(Booth::withTrashed()->where('number', 'A-01')->doesntExist())->toBeTrue()
        ->and(Hall::withTrashed()->where('number', 'Hall-A')->doesntExist())->toBeTrue()
        ->and(EventHall::query()->where('number', '1')->doesntExist())->toBeTrue()
        ->and(Hall::query()->whereIn('number', ['7-1', '7-2', '7-3', '7-4'])->count())->toBe(4)
        ->and(Hall::query()->whereIn('number', ['8-1', '8-2', '8-3', '8-4'])->count())->toBe(4)
        ->and(EventHall::query()->whereIn('number', ['M3', 'M3.1', 'M3.2', 'M6', 'M6.1'])->count())->toBe(5)
        ->and(Facility::query()->where('type', 'bathroom')->count())->toBe(8)
        ->and(Facility::query()->where('type', 'bathroom')->where('gender', 'male')->count())->toBe(4)
        ->and(Facility::query()->where('type', 'bathroom')->where('gender', 'female')->count())->toBe(4);

    expect(Hall::query()->where([
        'number' => '1',
        'area' => 2210.0,
        'svg_id' => '1A-00',
    ])->exists())->toBeTrue()
        ->and(Hall::query()->where([
            'number' => '26',
            'area' => 4522.0,
            'svg_id' => '26E-00',
        ])->exists())->toBeTrue()
        ->and(EventHall::query()->where([
            'number' => 'M4',
            'area' => 504.0,
            'price_per_hour' => 252000.0,
        ])->exists())->toBeTrue();
});

test('dependent seeders use booths and halls from the map', function () {
    (new UserSeeder)->run();
    (new CompanySeeder)->run();
    (new HallSeeder)->run();
    (new BoothSeeder)->run();
    (new BoothRequestSeeder)->run();
    (new HallSeeder)->run();
    (new BoothSeeder)->run();
    (new BoothRequestSeeder)->run();

    $expectedBoothCounts = [
        '1' => 42,
        '2' => 40,
        '7-1' => 14,
        '7-2' => 13,
        '7-3' => 13,
        '7-4' => 14,
        '8-1' => 22,
        '8-2' => 18,
        '8-3' => 22,
        '8-4' => 18,
        '10' => 26,
        '11' => 40,
        '13' => 14,
        '14' => 14,
        '15' => 13,
        '16' => 13,
        '25' => 49,
        '26' => 48,
        '36' => 28,
    ];

    foreach ($expectedBoothCounts as $hallNumber => $boothCount) {
        expect(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', $hallNumber))->count())
            ->toBe($boothCount);
    }

    expect(Booth::query()->count())->toBe(461)
        ->and(BoothRequest::query()->count())->toBe(3)
        ->and(Booth::query()->whereNull('company_id')->count())->toBe(457)
        ->and(Booth::query()->whereNotNull('company_id')->count())->toBe(4)
        ->and(Booth::query()->whereNull('company_id')->whereNotNull('qr_token')->doesntExist())->toBeTrue()
        ->and(DB::table('booth_system_users')->count())->toBe(4)
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '2'))->where('number', '2C-01')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '10'))->where('number', '10D-01')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '10'))->where(['number' => '10D-16', 'area' => 58.0])->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '11'))->where('number', '11F-01')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '15'))->where('number', '7P-28')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '16'))->where('number', '7R-28')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '25'))->where('number', '25B-01')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '25'))->where(['number' => '25B-33.1', 'area' => 64.0])->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '26'))->where('number', '26E-01')->exists())->toBeTrue()
        ->and(Booth::query()->whereHas('hall', fn ($query) => $query->where('number', '1'))->where(['number' => 'SE_40', 'area' => 56.0])->exists())->toBeTrue();
});
