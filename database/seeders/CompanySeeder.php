<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;

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
            'status' => Status::APPROVED,
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
            'status' => Status::APPROVED,
        ]);

        $companyC = Company::create([
            'name' => 'North Star Events',
            'business_sector' => 'Event Management',
            'social_links' => ['website' => 'https://northstar-events.example', 'instagram' => 'https://instagram.com/northstarevents'],
            'phone' => '+963115556667',
            'year_founded' => 2019,
            'description' => 'Independent event planner used for booth request testing.',
            'headquarters_lat' => 33.521100,
            'headquarters_lng' => 36.289900,
            'status' => Status::PENDING,
        ]);

        $companyD = Company::create([
            'name' => 'Artisan Market House',
            'business_sector' => 'Retail & Craft',
            'social_links' => ['website' => 'https://artisan-market.example', 'facebook' => 'https://facebook.com/artisanmarkethouse'],
            'phone' => '+963116667778',
            'year_founded' => 2012,
            'description' => 'Craft-focused retailer with no booth assignment in the seeded dataset.',
            'headquarters_lat' => 33.508400,
            'headquarters_lng' => 36.281200,
            'status' => Status::REJECTED,
        ]);

        $companyE = Company::create([
            'name' => 'Metro Tech Labs',
            'business_sector' => 'Technology',
            'social_links' => ['website' => 'https://metrotechlabs.example', 'linkedin' => 'https://linkedin.com/company/metrotechlabs'],
            'phone' => '+963117778889',
            'year_founded' => 2021,
            'description' => 'Technology vendor included as a separate company test record.',
            'headquarters_lat' => 33.515900,
            'headquarters_lng' => 36.294100,
            'status' => Status::PENDING,
        ]);

        $companyF = Company::create([
            'name' => 'Summit Retail Group',
            'business_sector' => 'Wholesale & Distribution',
            'social_links' => ['website' => 'https://summitretail.example', 'x' => 'https://x.com/summitretailgroup'],
            'phone' => '+963118889990',
            'year_founded' => 2010,
            'description' => 'Additional unassigned company for request and company testing.',
            'headquarters_lat' => 33.503300,
            'headquarters_lng' => 36.305700,
            'status' => Status::APPROVED,
        ]);

        $elcoach = SystemUser::where('name', 'Elcoach')->firstOrFail();
        $fawzy = SystemUser::where('name', 'Fawzy')->firstOrFail();
        $elza3eem = SystemUser::where('name', 'Elza3eem')->firstOrFail();

        $companyA->systemUsers()->syncWithoutDetaching([
            $elcoach->id,
            $fawzy->id,
        ]);

        $companyB->systemUsers()->syncWithoutDetaching([
            $elza3eem->id,
            $elcoach->id,
        ]);

        $companyC->systemUsers()->syncWithoutDetaching([
            $fawzy->id,
            $elcoach->id,
        ]);

        $companyD->systemUsers()->syncWithoutDetaching([
            $elcoach->id,
        ]);

        $companyE->systemUsers()->syncWithoutDetaching([
            $elza3eem->id,
            $elcoach->id,
        ]);

        $companyF->systemUsers()->syncWithoutDetaching([
            $fawzy->id,
            $elcoach->id,
            $elza3eem->id,
        ]);

        $companyALogo = database_path('assets/alawael.png');
        if (is_file($companyALogo)) {
            $companyA->addMedia($companyALogo)->toMediaCollection('logo');
        }

        $companyBLogo = database_path('assets/RBCs.png');
        if (is_file($companyBLogo)) {
            $companyB->addMedia($companyBLogo)->toMediaCollection('logo');
        }
    }
}
