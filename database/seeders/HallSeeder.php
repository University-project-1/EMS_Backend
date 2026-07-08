<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hall;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        $types = ['conference', 'exhibition'];

        foreach ($letters as $letter) {
            Hall::create([
                'number' => 'Hall-' . $letter,
                'area' => rand(500, 2000),
                'svg_id' => 'hall-' . strtolower($letter),
                'type' => $types[array_rand($types)],
            ]);
        }
    }
}
