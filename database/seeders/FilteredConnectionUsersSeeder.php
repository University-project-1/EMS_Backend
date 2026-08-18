<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class FilteredConnectionUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = FilteredConnectionUsersData::users();

        // Remove only the temporary SystemUsers created by the incorrect earlier
        // implementation, never an existing project identity.
        SystemUser::query()->where('email', 'like', '%@filtered-connections.test')->delete();

        foreach ($users as $definition) {
            $legacyEmail = str_replace('@approved-users.test', '@filtered-users.test', $definition['email']);
            $legacyUser = User::query()->where('email', $legacyEmail)->first();
            if ($legacyUser && ! User::query()->where('email', $definition['email'])->exists()) {
                $legacyUser->update(['email' => $definition['email']]);
            }

            User::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'first_name' => $definition['first_name'],
                    'last_name' => $definition['last_name'],
                    'phone' => $definition['phone'],
                    'location' => $definition['location'],
                    'job' => $definition['job'],
                    'gender' => $definition['gender'],
                    'birthday' => $definition['birthday'],
                    'password' => Hash::make('12345678'),
                ],
            );
        }
    }
}
