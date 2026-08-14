<?php

namespace Database\Seeders;

use App\Models\BusCatalog;
use Illuminate\Database\Seeder;

class BusCatalogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['location' => 'باب توما', 'start_time' => '08:00:00', 'end_time' => '21:00.00', 'duration' => 45],
            ['location' => 'جسر الحرية', 'start_time' => '09:00:00', 'end_time' => '21:00.00', 'duration' => 30],
            ['location' => 'جرمانا', 'start_time' => '10:00:00', 'end_time' => '22:00.00', 'duration' => 60],
            ['location' => 'شارع الثورة', 'start_time' => '11:00:00', 'end_time' => '21:00.00', 'duration' => 30],
            ['location' => 'حرستا', 'start_time' => '12:00:00', 'end_time' => '18:00.00', 'duration' => 90],
            ['location' => 'ساحة العباسيين', 'start_time' => '13:00:00', 'end_time' => '21:00.00', 'duration' => 30],
            ['location' => 'مشروع دمر', 'start_time' => '14:00:00', 'end_time' => '20:00.00', 'duration' => 60],
        ];

        foreach ($locations as $location) {
            BusCatalog::create($location);
        }
    }
}
