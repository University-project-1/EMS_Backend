<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\SystemUserType;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class RealTechPeopleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RealTechData::people() as $person) {
            SystemUser::query()->updateOrCreate(
                ['email' => $person['email']],
                [
                    'name' => $person['name'],
                    'password' => Hash::make('12345678'),
                    'type' => SystemUserType::EXHIBITOR->value,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
