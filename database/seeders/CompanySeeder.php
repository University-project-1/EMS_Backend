<?php

namespace Database\Seeders;

use App\Enum\BusinessSectors;
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
            'business_sector' => BusinessSectors::CULTURE,
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
            'business_sector' => BusinessSectors::FOOD_AND_BEVERAGE,
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
            'business_sector' => BusinessSectors::MEDIA,
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
            'business_sector' => BusinessSectors::HUMANITARIAN,
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
            'business_sector' => BusinessSectors::TECH,
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
            'business_sector' => BusinessSectors::TOURISM,
            'social_links' => ['website' => 'https://summitretail.example', 'x' => 'https://x.com/summitretailgroup'],
            'phone' => '+963118889990',
            'year_founded' => 2010,
            'description' => 'Additional unassigned company for request and company testing.',
            'headquarters_lat' => 33.503300,
            'headquarters_lng' => 36.305700,
            'status' => Status::APPROVED,
        ]);

        $companyG = Company::create([
            'name' => 'Al-Noor Publishing House',
            'business_sector' => BusinessSectors::CULTURE,
            'social_links' => ['website' => 'https://alnoor-publishing.example', 'instagram' => 'https://instagram.com/alnoorpublishing'],
            'phone' => '+963119990011',
            'year_founded' => 2017,
            'description' => 'Publishing house focused on books, magazines, and literary programs.',
            'headquarters_lat' => 33.512900,
            'headquarters_lng' => 36.279400,
            'status' => Status::APPROVED,
        ]);

        $companyH = Company::create([
            'name' => 'Cedar Build Works',
            'business_sector' => BusinessSectors::CONSTRUCTION,
            'social_links' => ['website' => 'https://cedarbuild.example', 'linkedin' => 'https://linkedin.com/company/cedarbuildworks'],
            'phone' => '+963120001122',
            'year_founded' => 2011,
            'description' => 'Construction and interior solutions provider for exhibition spaces.',
            'headquarters_lat' => 33.507600,
            'headquarters_lng' => 36.287300,
            'status' => Status::APPROVED,
        ]);

        $companyI = Company::create([
            'name' => 'Meridian Health Alliance',
            'business_sector' => BusinessSectors::HEALTHCARE,
            'social_links' => ['website' => 'https://meridian-health.example', 'facebook' => 'https://facebook.com/meridianhealthalliance'],
            'phone' => '+963121112233',
            'year_founded' => 2014,
            'description' => 'Healthcare group presenting medical services and outreach programs.',
            'headquarters_lat' => 33.516500,
            'headquarters_lng' => 36.292800,
            'status' => Status::APPROVED,
        ]);

        $companyJ = Company::create([
            'name' => 'Atlas Commerce Hub',
            'business_sector' => BusinessSectors::COMMERCE,
            'social_links' => ['website' => 'https://atlas-commerce.example', 'x' => 'https://x.com/atlascommercehub'],
            'phone' => '+963122223344',
            'year_founded' => 2020,
            'description' => 'Retail and wholesale hub for multi-brand exhibition participation.',
            'headquarters_lat' => 33.520200,
            'headquarters_lng' => 36.283900,
            'status' => Status::APPROVED,
        ]);

        $companyK = Company::create([
            'name' => 'SkyPoint Tourism Ventures',
            'business_sector' => BusinessSectors::TOURISM,
            'social_links' => ['website' => 'https://skypoint-tourism.example', 'instagram' => 'https://instagram.com/skypointtourism'],
            'phone' => '+963123334455',
            'year_founded' => 2018,
            'description' => 'Tourism operator used as a non-approved company fixture for testing.',
            'headquarters_lat' => 33.501100,
            'headquarters_lng' => 36.298600,
            'status' => Status::PENDING,
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

        $companyG->systemUsers()->syncWithoutDetaching([
            $elcoach->id,
            $fawzy->id,
        ]);

        $companyH->systemUsers()->syncWithoutDetaching([
            $elza3eem->id,
            $elcoach->id,
        ]);

        $companyI->systemUsers()->syncWithoutDetaching([
            $fawzy->id,
            $elza3eem->id,
        ]);

        $companyJ->systemUsers()->syncWithoutDetaching([
            $elcoach->id,
        ]);

        $companyK->systemUsers()->syncWithoutDetaching([
            $fawzy->id,
            $elcoach->id,
            $elza3eem->id,
        ]);

        $companyALogo = database_path('assets/alawael.png');
        if (is_file($companyALogo)) {
            $companyA->copyMedia($companyALogo)->toMediaCollection('logo');
        }

        $companyBLogo = database_path('assets/RBCs.png');
        if (is_file($companyBLogo)) {
            $companyB->copyMedia($companyBLogo)->toMediaCollection('logo');
        }

        $companyCLogo = database_path('assets/fawzy.jpg');
        if (is_file($companyCLogo)) {
            $companyC->copyMedia($companyCLogo)->toMediaCollection('logo');
        }

        $companyDLogo = database_path('assets/RGBs.jpg');
        if (is_file($companyDLogo)) {
            $companyD->copyMedia($companyDLogo)->toMediaCollection('logo');
        }

        $companyGLogo = database_path('assets/elsaadeh.png');
        if (is_file($companyGLogo)) {
            $companyG->copyMedia($companyGLogo)->toMediaCollection('logo');
        }

        $companyHLogo = database_path('assets/Elba3eth.png');
        if (is_file($companyHLogo)) {
            $companyH->copyMedia($companyHLogo)->toMediaCollection('logo');
        }

        $companyILogo = database_path('assets/wasem.png');
        if (is_file($companyILogo)) {
            $companyI->copyMedia($companyILogo)->toMediaCollection('logo');
        }

        $companyJLogo = database_path('assets/RBCs.png');
        if (is_file($companyJLogo)) {
            $companyJ->copyMedia($companyJLogo)->toMediaCollection('logo');
        }

        $companyKLogo = database_path('assets/RGBs.jpg');
        if (is_file($companyKLogo)) {
            $companyK->copyMedia($companyKLogo)->toMediaCollection('logo');
        }

        $galleryAssets = ['alawael.png', 'Elba3eth.png', 'elsaadeh.png', 'fawzy.jpg', 'RBCs.png', 'RGBs.jpg', 'wasem.png'];
        $companies = [$companyA, $companyB, $companyC, $companyD, $companyE, $companyF, $companyG, $companyH, $companyI, $companyJ, $companyK];

        foreach ($companies as $index => $company) {
            $assetCount = count($galleryAssets);
            $this->syncGallery($company, [
                $galleryAssets[$index % $assetCount],
                $galleryAssets[($index + 1) % $assetCount],
            ]);
        }
    }

    /**
     * @param  array<int, string>  $assets
     */
    private function syncGallery(Company $company, array $assets): void
    {
        $company->clearMediaCollection('gallery');

        foreach ($assets as $asset) {
            $assetPath = database_path('assets/'.$asset);

            if (! is_file($assetPath)) {
                throw new \RuntimeException("Missing company gallery asset: {$asset}");
            }

            $company->copyMedia($assetPath)->toMediaCollection('gallery');
        }
    }
}
