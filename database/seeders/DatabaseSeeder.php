<?php

namespace Database\Seeders;

use App\Models\Booth;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PassportSeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            HallSeeder::class,
            BoothSeeder::class,
            EventSeeder::class,
        ]);
    }

}
