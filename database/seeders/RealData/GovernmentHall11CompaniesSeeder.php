<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\Company;
use Illuminate\Database\Seeder;

final class GovernmentHall11CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (GovernmentHall11Data::companies() as $slug => $definition) {
            Company::query()->updateOrCreate(
                ['name' => $definition['name']],
                [
                    'business_sector' => BusinessSectors::from($definition['sector'])->value,
                    'description' => $definition['description'],
                    'social_links' => array_filter([
                        'website' => $definition['website'] ?? null,
                        'linkedin' => $definition['linkedin'] ?? null,
                        'facebook' => $definition['facebook'] ?? null,
                        'dataset_key' => $slug,
                    ]),
                    'phone' => '+963 11 000 0000',
                    'year_founded' => $definition['year'],
                    'status' => Status::APPROVED->value,
                ],
            );
        }
    }
}
