<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Company;
use Illuminate\Database\Seeder;

final class RealTechCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RealTechData::companies() as $slug => $company) {
            Company::query()->updateOrCreate(
                ['name' => $company['name']],
                [
                    'business_sector' => $company['sector'],
                    'description' => $company['description'],
                    'social_links' => [
                        'linkedin' => $company['linkedin'],
                        'website' => $company['website'],
                        'dataset_key' => $slug,
                    ],
                    'phone' => '+963 000 000 000',
                    'year_founded' => $company['year'],
                    'status' => Status::APPROVED->value,
                ],
            );
        }
    }
}
