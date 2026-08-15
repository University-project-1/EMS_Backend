<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // The legacy seeders predate idempotent re-runs. Keep them for a fresh
        // database, but do not run them again over an existing dev database.
        if (! DB::table('users')->exists() && ! DB::table('system_users')->exists()) {
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
                SavedSeeder::class,
                NotificationSeeder::class,
                ReportSeeder::class,
                ReviewSeeder::class,
            ]);
        }

        // =====================================================================
        // CLEAN REAL-TECH DATA — separate from the legacy seeders above.
        // This section creates no Events and reuses existing sectors, services,
        // halls, and booths. Run order is dependency-safe.
        // =====================================================================
        $this->call([
            RealTechPeopleSeeder::class,
            RealTechCompaniesSeeder::class,
            RealTechCompanyMembershipsSeeder::class,
            RealTechBoothBookingsSeeder::class,
            RealTechMediaSeeder::class,
        ]);
    }
}
