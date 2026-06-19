<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventHall;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventHall::create([
            'number' => 1,
            'area' => 100.0,
            'svg_id' => 'event-a01',
            'price_per_hour' => 50000,
        ]);

        EventHall::create([
            'number' => 2,
            'area' => 150.0,
            'svg_id' => 'event-a02',
            'price_per_hour' => 75000,
        ]);

        EventHall::create([
            'number' => 3,
            'area' => 100.0,
            'svg_id' => 'event-a03',
            'price_per_hour' => 50000,
        ]);

        EventHall::create([
            'number' => 4,
            'area' => 200.0,
            'svg_id' => 'event-a04',
            'price_per_hour' => 100000.00,
        ]);
    }
}
