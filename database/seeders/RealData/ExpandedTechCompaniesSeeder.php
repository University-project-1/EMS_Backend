<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\Company;
use Illuminate\Database\Seeder;

final class ExpandedTechCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ExpandedTechData::companies() as $slug => $company) {
            Company::query()->updateOrCreate(
                ['name' => $company['name']],
                [
                    'business_sector' => BusinessSectors::TECH->value,
                    'description' => $company['description'],
                    'social_links' => ['linkedin' => $company['linkedin'], 'website' => $company['website'], 'dataset_key' => $slug],
                    'phone' => '+963 000 000 000',
                    'year_founded' => $company['year'],
                    'status' => Status::APPROVED->value,
                ],
            );
        }
    }
}
