<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\LeadSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AnnouncementSeeder::class,
            PassportSeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            ServiceSeeder::class,
            BusCatalogsSeeder::class,
            HallSeeder::class,
            FacilitySeeder::class,
            BoothSeeder::class,
            BoothRequestSeeder::class,
            EventSeeder::class,
            LeadSeeder::class,
            NotificationSeeder::class,
            ReportSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
