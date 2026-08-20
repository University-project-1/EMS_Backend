<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Models\Company;
use App\Models\CompanySystemUser;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class RealTechCompanyMembershipsSeeder extends Seeder
{
    public function run(): void
    {
        $people = [];
        foreach (RealTechData::people() as $person) {
            $people[$person['key']] = SystemUser::query()->where('email', $person['email'])->firstOrFail();
        }

        foreach (RealTechData::people() as $person) {
            $company = Company::query()->where('name', RealTechData::companies()[$person['company']]['name'])->firstOrFail();
            DB::table('company_system_users')->updateOrInsert(
                ['company_id' => $company->id, 'system_user_id' => $people[$person['key']]->id],
                ['assigned_by' => null, 'created_at' => now()],
            );
        }
    }
}
