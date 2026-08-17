<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Ensures every approved company in the seeded exhibition scenario has at least
 * one manager membership without creating duplicate SystemUser identities.
 * The people are existing canonical demo identities; membership is the only
 * relationship being added here.
 */
final class ApprovedCompanyManagerBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            'Syria Smart' => 'anas.ajaj@example.test',
            'Sham Cash' => 'mohammed.fawzy.sukkar@example.test',
            'SAP' => 'arvind.krishna@example.test',
            'Huawei' => 'nizar.zarka@expanded-tech.test',
            'Directorate-General of Antiquities and Museums' => 'mustafa.al.mousa@government-hall11.test',
        ];

        foreach ($assignments as $companyName => $email) {
            $company = Company::query()->where('name', $companyName)->where('status', 'approved')->first();
            $user = SystemUser::query()->where('email', $email)->first();

            if (! $company || ! $user) {
                continue;
            }

            DB::table('company_system_users')->updateOrInsert(
                ['company_id' => $company->id, 'system_user_id' => $user->id],
                ['assigned_by' => null, 'created_at' => now()],
            );
        }
    }
}

