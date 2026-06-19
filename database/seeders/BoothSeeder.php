<?php

namespace Database\Seeders;

use App\Models\Hall;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booth;
use App\Models\Company;
use Illuminate\Support\Str;

class BoothSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halls = Hall::all();
        $companies = Company::all();
        
        Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-01',
            'svg_id' => 'booth-a01',
            'area' => 9.0,
            'price' => 250.00,
        ]);

        Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-02',
            'svg_id' => 'booth-a02',
            'area' => 12.0,
            'price' => 300.00,
        ]);

        Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-03',
            'svg_id' => 'booth-a03',
            'area' => 6.0,
            'price' => 180.00,
        ]);

        Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'B-01',
            'svg_id' => 'booth-b01',
            'area' => 8.5,
            'price' => 200.00,
        ]);

        Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => null,
            'qr_token' => (string) Str::uuid(),
            'number' => 'B-02',
            'svg_id' => 'booth-b02',
            'area' => 7.0,
            'price' => 160.00,
        ]);
    }
}
