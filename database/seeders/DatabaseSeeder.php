<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if ((int) DB::table('companies')->count() === 0) {
            $this->call([
                AnnouncementSeeder::class,
            ExhibitionAnnouncementsSeeder::class,
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

        $this->call([
            RealTechPeopleSeeder::class,
            RealTechCompaniesSeeder::class,
            RealTechCompanyMembershipsSeeder::class,
            RealTechBoothBookingsSeeder::class,
            RealTechMediaSeeder::class,
            ExpandedTechPeopleSeeder::class,
            ExpandedTechCompaniesSeeder::class,
            ExpandedTechCompanyMembershipsSeeder::class,
            ExpandedTechMediaSeeder::class,
            ExpandedTechBoothBookingsSeeder::class,
            GovernmentHall11CompaniesSeeder::class,
            GovernmentHall11PeopleSeeder::class,
            GovernmentHall11MembershipsSeeder::class,
            GovernmentHall11MediaSeeder::class,
            GovernmentHall11BoothBookingsSeeder::class,
            GovernmentHall11InvitationsSeeder::class,
            ExhibitionWeekEventsSeeder::class,
            ApprovedCompanyManagerBackfillSeeder::class,
        ]);
    }
}
