<?php

namespace Database\Seeders;

use App\Models\EventHall;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * The M labels are independent event halls. Areas missing from the map are
     * stable estimates, and prices follow the existing 500-per-square-metre rate.
     *
     * @var list<array{number: string, area: float, svg_id: string, price_per_hour: float}>
     */
    private const EVENT_HALLS = [
        ['number' => 'M1', 'area' => 120.0, 'svg_id' => 'event-hall-m1', 'price_per_hour' => 60000.0],
        ['number' => 'M2', 'area' => 100.0, 'svg_id' => 'event-hall-m2', 'price_per_hour' => 50000.0],
        ['number' => 'M3', 'area' => 288.0, 'svg_id' => 'event-hall-m3', 'price_per_hour' => 144000.0],
        ['number' => 'M3.1', 'area' => 76.0, 'svg_id' => 'event-hall-m3-1', 'price_per_hour' => 38000.0],
        ['number' => 'M3.2', 'area' => 75.0, 'svg_id' => 'event-hall-m3-2', 'price_per_hour' => 37500.0],
        ['number' => 'M4', 'area' => 504.0, 'svg_id' => 'event-hall-m4', 'price_per_hour' => 252000.0],
        ['number' => 'M5', 'area' => 65.0, 'svg_id' => 'event-hall-m5', 'price_per_hour' => 32500.0],
        ['number' => 'M6', 'area' => 675.0, 'svg_id' => 'event-hall-m6', 'price_per_hour' => 337500.0],
        ['number' => 'M6.1', 'area' => 188.0, 'svg_id' => 'event-hall-m6-1', 'price_per_hour' => 94000.0],
        ['number' => 'M7', 'area' => 390.0, 'svg_id' => 'event-hall-m7', 'price_per_hour' => 195000.0],
        ['number' => 'M8', 'area' => 140.0, 'svg_id' => 'event-hall-m8', 'price_per_hour' => 70000.0],
        ['number' => 'M9', 'area' => 60.0, 'svg_id' => 'event-hall-m9', 'price_per_hour' => 30000.0],
        ['number' => 'M10', 'area' => 100.0, 'svg_id' => 'event-hall-m10', 'price_per_hour' => 50000.0],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventHall::query()
            ->whereIn('number', ['1', '2', '3', '4'])
            ->whereDoesntHave('events')
            ->delete();

        foreach (self::EVENT_HALLS as $eventHallData) {
            EventHall::query()->updateOrCreate(
                ['number' => $eventHallData['number']],
                $eventHallData,
            );
        }
    }
}
