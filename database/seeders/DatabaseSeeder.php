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
            RealData\FilteredConnectionUsersSeeder::class,
            RealData\FilteredConnectionUsersMediaSeeder::class,
            RealData\RealTechPeopleSeeder::class,
            RealData\RealTechCompaniesSeeder::class,
            RealData\RealTechCompanyMembershipsSeeder::class,
            RealData\RealTechBoothBookingsSeeder::class,
            RealData\RealTechMediaSeeder::class,
            RealData\ExpandedTechPeopleSeeder::class,
            RealData\ExpandedTechCompaniesSeeder::class,
            RealData\ExpandedTechCompanyMembershipsSeeder::class,
            RealData\ExpandedTechMediaSeeder::class,
            RealData\ExpandedTechBoothBookingsSeeder::class,
            RealData\GovernmentHall11CompaniesSeeder::class,
            RealData\GovernmentHall11PeopleSeeder::class,
            RealData\GovernmentHall11MembershipsSeeder::class,
            RealData\GovernmentHall11MediaSeeder::class,
            RealData\GovernmentHall11BoothBookingsSeeder::class,
            RealData\GovernmentHall11InvitationsSeeder::class,
            RealData\ExhibitionWeekEventsSeeder::class,
            RealData\RealDataUserInteractionsSeeder::class,
            RealData\ApprovedCompanyManagerBackfillSeeder::class,
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
            ReportSeeder::class,
            ReviewSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
