<?php

namespace Database\Seeders;

use App\Models\BusCatalog;
use Illuminate\Database\Seeder;

class BusCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['location' => 'باب توما', 'start_time' => '08:00:00', 'end_time' => '08:45:00', 'duration' => 45],
            ['location' => 'جسر الحرية', 'start_time' => '09:00:00', 'end_time' => '09:45:00', 'duration' => 45],
            ['location' => 'جرمانا', 'start_time' => '10:00:00', 'end_time' => '10:45:00', 'duration' => 45],
            ['location' => 'شارع الثورة', 'start_time' => '11:00:00', 'end_time' => '11:45:00', 'duration' => 45],
            ['location' => 'حرستا', 'start_time' => '12:00:00', 'end_time' => '12:45:00', 'duration' => 45],
            ['location' => 'ساحة العباسيين', 'start_time' => '13:00:00', 'end_time' => '13:45:00', 'duration' => 45],
            ['location' => 'مشروع دمر', 'start_time' => '14:00:00', 'end_time' => '14:45:00', 'duration' => 45],
        ];

        foreach ($locations as $location) {
            BusCatalog::create($location);
        }
    }
}
