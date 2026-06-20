<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyA = Company::create([
            'name' => 'Dar Al feker',
            'business_sector' => 'Lectures & Exhibitions',
            'social_links' => ['website' => 'https://dar.com', 'linkedin' => 'https://linkedin.com/company/dar'],
            'phone' => '+963112223334',
            'year_founded' => 2015,
            'description' => 'Leading readers for reading.',
            'headquarters_lat' => 33.513807,
            'headquarters_lng' => 36.276528,
        ]);

        $companyB = Company::create([
            'name' => 'GreenFoods Co.',
            'business_sector' => 'Food & Beverage',
            'social_links' => ['website' => 'https://greenfoods.example', 'facebook' => 'https://facebook.com/greenfoods'],
            'phone' => '+963114445556',
            'year_founded' => 2008,
            'description' => 'Organic, sustainable food producer with regional distribution.',
            'headquarters_lat' => 33.500000,
            'headquarters_lng' => 36.300000,
        ]);
    }
}
