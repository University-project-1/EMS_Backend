<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class GovernmentHall11MembershipsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [];
        foreach (GovernmentHall11Data::companies() as $slug => $definition) {
            $companies[$slug] = Company::query()->where('name', $definition['name'])->firstOrFail();
        }

        foreach (GovernmentHall11Data::people() as $person) {
            $user = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            foreach ($person['companies'] as $companyKey) {
                $company = $companies[$companyKey];
                DB::table('company_system_users')->updateOrInsert(
                    ['company_id' => $company->id, 'system_user_id' => $user->id],
                    ['assigned_by' => null, 'created_at' => now()],
                );
            }
        }
    }
}
