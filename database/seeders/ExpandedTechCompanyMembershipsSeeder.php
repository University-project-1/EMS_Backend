<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ExpandedTechCompanyMembershipsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [];
        foreach (ExpandedTechData::companies() as $slug => $definition) {
            $companies[$slug] = Company::query()->where('name', $definition['name'])->firstOrFail();
        }

        foreach (ExpandedTechData::people() as $person) {
            $user = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            foreach ($person['memberships'] as $membership) {
                DB::table('company_system_users')->updateOrInsert(
                    ['company_id' => $companies[$membership['company']]->id, 'system_user_id' => $user->id],
                    ['assigned_by' => null, 'created_at' => now()],
                );
            }
        }
    }
}
