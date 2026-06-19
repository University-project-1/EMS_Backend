<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hall;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hallMain = Hall::create([
            'number' => 'Main-1',
            'area' => 1500.0,
            'svg_id' => 'hall-main-1',
            'type' => 'exhibition',
        ]);

        $hallB = Hall::create([
            'number' => 'Hall-B',
            'area' => 800.0,
            'svg_id' => 'hall-b',
            'type' => 'conference',
        ]);
    }
}
