<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ServiceSeeder;

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
            HallSeeder::class,
            BoothSeeder::class,
            BoothRequestSeeder::class,
            EventSeeder::class,
        ]);
    }
}
