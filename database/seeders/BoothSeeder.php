<?php

namespace Database\Seeders;

use App\Models\Booth;
use App\Models\Hall;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class BoothSeeder extends Seeder
{
    private const PRICE_PER_SQUARE_METRE = 25.0;

    /** @var list<float> */
    private const HALL_1_AREAS = [
        18.0, 18.0, 18.0, 18.0, 18.0, 20.0, 25.0, 25.0, 20.0, 36.0, 45.5, 18.0, 15.0, 15.0,
        15.0, 25.5, 15.0, 18.0, 18.0, 15.0, 35.0, 35.0, 35.0, 35.0, 26.0, 22.0, 30.0, 13.5,
        40.5, 45.0, 18.0, 18.0, 18.0, 18.0, 18.0, 40.0, 45.0, 45.0, 36.0, 56.0, 56.0, 56.0,
    ];

    /** @var list<float> */
    private const HALL_2_AREAS = [
        28.5, 16.5, 30.0, 30.0, 30.0, 30.0, 30.0, 30.0, 18.0, 18.0, 19.5, 21.0, 21.0, 21.0,
        27.0, 21.0, 21.0, 21.0, 21.0, 21.0, 15.0, 22.0, 38.0, 50.0, 60.0, 60.0, 50.0, 35.0,
        35.0, 84.0, 98.0, 91.0, 49.0, 65.0, 65.0, 91.0, 42.0, 42.0, 60.0, 60.0,
    ];

    /** @var list<float> */
    private const HALL_10_AREAS = [
        21.0, 21.0, 27.0, 24.0, 21.0, 21.0, 21.0, 27.0, 21.0, 21.0, 21.0, 24.0, 30.0,
        24.0, 24.0, 58.0, 58.0, 58.0, 75.0, 81.0, 94.5, 94.5, 94.5, 91.0, 89.0, 94.5,
    ];

    /** @var list<float> */
    private const HALL_11_AREAS = [
        28.5, 16.5, 30.0, 30.0, 30.0, 30.0, 30.0, 30.0, 18.0, 18.0, 19.5, 21.0, 21.0, 21.0,
        27.0, 21.0, 21.0, 21.0, 21.0, 21.0, 15.0, 22.0, 38.0, 50.0, 50.0, 35.0, 35.0, 84.0,
        60.0, 60.0, 70.0, 70.0, 98.0, 91.0, 65.0, 65.0, 60.0, 60.0, 42.0, 42.0,
    ];

    /** @var array<int|string, float> */
    private const HALL_25_AREAS = [
        1 => 48.0, 2 => 48.0, 3 => 22.0, 4 => 22.0, 5 => 22.0, 6 => 22.0, 7 => 22.0, 8 => 22.0,
        9 => 22.0, 10 => 22.0, 11 => 22.0, 12 => 22.0, 13 => 18.0, 14 => 18.0, 15 => 24.0,
        16 => 24.0, 17 => 18.0, 18 => 18.0, 19 => 22.0, 20 => 22.0, 21 => 22.0, 22 => 22.0,
        23 => 22.0, 24 => 22.0, 25 => 22.0, 26 => 22.0, 27 => 22.0, 28 => 22.0, 29 => 22.0,
        30 => 48.0, 31 => 48.0, 32 => 128.0, 33 => 64.0, '33.1' => 64.0, 34 => 64.0, '34.1' => 64.0,
        35 => 128.0, 36 => 100.0, 37 => 100.0, 38 => 96.0, 39 => 96.0, 40 => 96.0, 41 => 96.0,
        42 => 100.0, 43 => 100.0, 44 => 128.0, 45 => 128.0, 46 => 128.0, 47 => 128.0,
    ];

    /** @var array<int|string, float> */
    private const HALL_26_AREAS = [
        1 => 48.0, 2 => 48.0, 3 => 22.0, 4 => 22.0, 5 => 22.0, 6 => 22.0, 7 => 22.0, 8 => 22.0,
        9 => 22.0, 10 => 22.0, 11 => 22.0, 12 => 22.0, 13 => 18.0, 14 => 18.0, 15 => 24.0,
        16 => 24.0, 17 => 18.0, 18 => 18.0, 19 => 22.0, 20 => 22.0, 21 => 22.0, 22 => 22.0,
        23 => 22.0, 24 => 22.0, 25 => 22.0, 26 => 22.0, 27 => 22.0, 28 => 22.0, 29 => 22.0,
        30 => 48.0, 31 => 48.0, 32 => 72.0, '32.1' => 56.0, 33 => 128.0, 34 => 128.0, 35 => 128.0,
        36 => 100.0, 37 => 100.0, 38 => 96.0, 39 => 96.0, 40 => 96.0, 41 => 96.0, 42 => 100.0,
        43 => 100.0, 44 => 128.0, 45 => 128.0, 46 => 128.0, 47 => 128.0,
    ];

    /** @var list<float> */
    private const HALL_36_AREAS = [
        21.0, 21.0, 30.0, 21.0, 21.0, 21.0, 21.0, 21.0, 21.0, 16.5, 21.0, 21.0, 27.0, 21.0,
        21.0, 21.0, 21.0, 21.0, 21.0, 21.0, 84.0, 85.0, 85.0, 80.0, 80.0, 85.0, 51.0, 80.0,
    ];

    /** @var list<float> */
    private const HALL_7_AREAS = [
        18.0, 18.0, 18.0, 27.0, 27.0, 18.0, 18.0, 18.0, 18.0, 28.5, 28.5, 18.0, 18.0, 18.0,
        18.0, 18.0, 18.0, 30.0, 31.5, 21.0, 21.0, 21.0, 31.5, 30.0, 24.0, 24.0, 24.0,
        30.0, 31.5, 21.0, 21.0, 21.0, 31.5, 30.0, 18.0, 18.0, 18.0, 24.0, 24.0, 24.0,
        18.0, 18.0, 18.0, 27.0, 27.0, 18.0, 18.0, 18.0, 18.0, 28.5, 28.5, 24.0, 24.0, 24.0,
    ];

    /** @var list<float> */
    private const HALL_8_K_AREAS = [
        23.0, 12.0, 12.0, 18.0, 8.0, 8.0, 8.0, 18.0, 11.25, 11.25, 11.25, 12.0, 12.0, 23.0,
        13.5, 15.0, 15.75, 15.75,
    ];

    /** @var list<float> */
    private const HALL_8_LM_AREAS = [
        23.0, 12.0, 12.0, 11.25, 11.25, 11.25, 18.0, 8.0, 8.0, 8.0, 18.0, 15.0, 15.0, 12.0,
        12.0, 23.0, 31.5, 15.0, 15.0, 13.5, 13.5, 15.0,
    ];

    /** @var list<float> */
    private const HALL_8_N_AREAS = [
        23.0, 12.0, 12.0, 18.0, 8.0, 8.0, 8.0, 18.0, 11.5, 11.5, 11.5, 12.0, 12.0, 23.0,
        13.5, 15.0, 15.75, 15.75,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = $this->boothDefinitions();
        $hallNumbers = array_values(array_unique(array_column($definitions, 'hall_number')));
        $halls = Hall::query()->whereIn('number', $hallNumbers)->get()->keyBy('number');

        DB::transaction(function () use ($definitions, $halls): void {
            $seededBoothIds = [];

            foreach ($definitions as $definition) {
                $hall = $halls->get($definition['hall_number']);

                if (! $hall instanceof Hall) {
                    throw new LogicException("Missing hall {$definition['hall_number']} for booth seeding.");
                }

                $booth = Booth::withTrashed()->firstOrNew([
                    'hall_id' => $hall->id,
                    'number' => $definition['number'],
                ]);
                $booth->fill([
                    'company_id' => null,
                    'area' => $definition['area'],
                    'price' => $definition['area'] * self::PRICE_PER_SQUARE_METRE,
                    'svg_id' => $definition['number'],
                    'qr_token' => null,
                ]);
                $booth->deleted_at = null;
                $booth->save();
                $booth->clearMediaCollection('qr_code');

                $seededBoothIds[] = $booth->id;
            }

            DB::table('booth_system_users')->whereIn('booth_id', $seededBoothIds)->delete();
        });
    }

    /**
     * @return list<array{hall_number: string, number: string, area: float}>
     */
    private function boothDefinitions(): array
    {
        $definitions = [];

        $this->appendSeries($definitions, '1', 'SE_', self::HALL_1_AREAS);
        $this->appendSeries($definitions, '2', '2C-', self::HALL_2_AREAS);
        $this->appendSeries($definitions, '10', '10D-', self::HALL_10_AREAS);
        $this->appendSeries($definitions, '11', '11F-', self::HALL_11_AREAS);
        $this->appendMappedSeries($definitions, '25', '25B-', self::HALL_25_AREAS);
        $this->appendMappedSeries($definitions, '26', '26E-', self::HALL_26_AREAS);
        $this->appendSeries($definitions, '36', '36JD-', self::HALL_36_AREAS);

        $this->appendSeries($definitions, '7-4', '7P-', array_slice(self::HALL_7_AREAS, 0, 14), 1);
        $this->appendSeries($definitions, '7-3', '7P-', array_slice(self::HALL_7_AREAS, 14, 13), 15);
        $this->appendSeries($definitions, '15', '7P-', array_slice(self::HALL_7_AREAS, 27, 13), 28);
        $this->appendSeries($definitions, '13', '7P-', array_slice(self::HALL_7_AREAS, 40, 14), 41);
        $this->appendSeries($definitions, '7-1', '7R-', array_slice(self::HALL_7_AREAS, 0, 14), 1);
        $this->appendSeries($definitions, '7-2', '7R-', array_slice(self::HALL_7_AREAS, 14, 13), 15);
        $this->appendSeries($definitions, '16', '7R-', array_slice(self::HALL_7_AREAS, 27, 13), 28);
        $this->appendSeries($definitions, '14', '7R-', array_slice(self::HALL_7_AREAS, 40, 14), 41);

        $this->appendSeries($definitions, '8-2', '8K-', self::HALL_8_K_AREAS);
        $this->appendSeries($definitions, '8-1', '8L-', self::HALL_8_LM_AREAS);
        $this->appendSeries($definitions, '8-3', '8M-', self::HALL_8_LM_AREAS);
        $this->appendSeries($definitions, '8-4', '8N-', self::HALL_8_N_AREAS);

        return $definitions;
    }

    /**
     * @param  list<array{hall_number: string, number: string, area: float}>  $definitions
     * @param  list<float>  $areas
     */
    private function appendSeries(
        array &$definitions,
        string $hallNumber,
        string $prefix,
        array $areas,
        int $startingNumber = 1,
    ): void {
        foreach ($areas as $index => $area) {
            $definitions[] = [
                'hall_number' => $hallNumber,
                'number' => $prefix.str_pad((string) ($startingNumber + $index), 2, '0', STR_PAD_LEFT),
                'area' => $area,
            ];
        }
    }

    /**
     * @param  list<array{hall_number: string, number: string, area: float}>  $definitions
     * @param  array<int|string, float>  $areas
     */
    private function appendMappedSeries(array &$definitions, string $hallNumber, string $prefix, array $areas): void
    {
        foreach ($areas as $suffix => $area) {
            $suffix = (string) $suffix;
            $formattedSuffix = str_contains($suffix, '.')
                ? $suffix
                : str_pad($suffix, 2, '0', STR_PAD_LEFT);

            $definitions[] = [
                'hall_number' => $hallNumber,
                'number' => $prefix.$formattedSuffix,
                'area' => $area,
            ];
        }
    }
}
