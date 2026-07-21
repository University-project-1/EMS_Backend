<?php

namespace Database\Seeders;

use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Hall;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    /** @var list<string> */
    private const LEGACY_BOOTH_NUMBERS = ['A-01', 'A-02', 'A-03', 'B-01', 'B-02'];

    /** @var list<string> */
    private const LEGACY_HALL_NUMBERS = [
        'Hall-A',
        'Hall-B',
        'Hall-C',
        'Hall-D',
        'Hall-E',
        'Hall-F',
        'Hall-G',
        'Hall-H',
        'Hall-I',
        'Hall-J',
    ];

    /**
     * @var list<array{number: string, area: float, svg_id: string, type: string}>
     */
    private const HALLS = [
        ['number' => '1', 'area' => 2210.0, 'svg_id' => '1A-00', 'type' => 'exhibition'],
        ['number' => '2', 'area' => 2845.0, 'svg_id' => '2C-00', 'type' => 'exhibition'],
        ['number' => '4', 'area' => 3200.0, 'svg_id' => 'hall-4', 'type' => 'exhibition'],
        ['number' => '5', 'area' => 950.0, 'svg_id' => 'hall-5', 'type' => 'exhibition'],
        ['number' => '6', 'area' => 950.0, 'svg_id' => 'hall-6', 'type' => 'exhibition'],
        ['number' => '7-1', 'area' => 420.0, 'svg_id' => 'hall-7-1', 'type' => 'exhibition'],
        ['number' => '7-2', 'area' => 420.0, 'svg_id' => 'hall-7-2', 'type' => 'exhibition'],
        ['number' => '7-3', 'area' => 420.0, 'svg_id' => 'hall-7-3', 'type' => 'exhibition'],
        ['number' => '7-4', 'area' => 420.0, 'svg_id' => 'hall-7-4', 'type' => 'exhibition'],
        ['number' => '8-1', 'area' => 380.0, 'svg_id' => 'hall-8-1', 'type' => 'exhibition'],
        ['number' => '8-2', 'area' => 380.0, 'svg_id' => 'hall-8-2', 'type' => 'exhibition'],
        ['number' => '8-3', 'area' => 380.0, 'svg_id' => 'hall-8-3', 'type' => 'exhibition'],
        ['number' => '8-4', 'area' => 380.0, 'svg_id' => 'hall-8-4', 'type' => 'exhibition'],
        ['number' => '10', 'area' => 2210.0, 'svg_id' => '10D-00', 'type' => 'exhibition'],
        ['number' => '11', 'area' => 2845.0, 'svg_id' => '11F-00', 'type' => 'exhibition'],
        ['number' => '13', 'area' => 500.0, 'svg_id' => 'hall-13', 'type' => 'exhibition'],
        ['number' => '14', 'area' => 500.0, 'svg_id' => 'hall-14', 'type' => 'exhibition'],
        ['number' => '15', 'area' => 500.0, 'svg_id' => 'hall-15', 'type' => 'exhibition'],
        ['number' => '16', 'area' => 500.0, 'svg_id' => 'hall-16', 'type' => 'exhibition'],
        ['number' => '25', 'area' => 4522.0, 'svg_id' => '25B-00', 'type' => 'exhibition'],
        ['number' => '26', 'area' => 4522.0, 'svg_id' => '26E-00', 'type' => 'exhibition'],
        ['number' => '36', 'area' => 850.0, 'svg_id' => 'hall-36', 'type' => 'exhibition'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->removeLegacyMapSeedData();

        foreach (self::HALLS as $hallData) {
            $hall = Hall::withTrashed()->updateOrCreate(
                ['number' => $hallData['number']],
                $hallData,
            );

            if ($hall->trashed()) {
                $hall->restore();
            }
        }
    }

    private function removeLegacyMapSeedData(): void
    {
        $legacyBoothIds = Booth::withTrashed()
            ->whereIn('number', self::LEGACY_BOOTH_NUMBERS)
            ->pluck('id');

        if ($legacyBoothIds->isNotEmpty()) {
            BoothRequest::withTrashed()
                ->whereIn('booth_id', $legacyBoothIds)
                ->forceDelete();

            Booth::withTrashed()
                ->whereIn('id', $legacyBoothIds)
                ->forceDelete();
        }

        Hall::withTrashed()
            ->whereIn('number', self::LEGACY_HALL_NUMBERS)
            ->whereDoesntHave('booths')
            ->forceDelete();
    }
}
