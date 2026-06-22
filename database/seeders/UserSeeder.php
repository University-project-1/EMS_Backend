<?php

namespace Database\Seeders;

use App\Enum\SystemUserType;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Wasem',
            'last_name' => 'Alhariri',
            'email' => 'wasemalhariri13@gmail.com',
            'phone' => '+963994801706',
            'location' => 'Syria,Damascus',
            'job' => 'Software Engineer',
            'gender' => 'male',
            'password' => '12345678',
            'birthday' => '2006-02-06',
        ]);

        SystemUser::create([
            'name' => 'Fawzy',
            'email' => 'fawzy.sukkar2005@gmail.com',
            'password' => '12345678',
            'type' => SystemUserType::ADMIN,
            'email_verified_at' => now(),
            ]);

        SystemUser::create([
            'name' => 'Elza3eem',
            'email' => 'abdalrahmansalloum200@gmail.com',
            'type' => SystemUserType::ADMIN,
            'password' => '12345678',
            'email_verified_at' => now(),
        ]);

        SystemUser::create([
            'name' => 'Elcoach',
            'email' => 'zuheiralhomsi73@gmail.com',
            'type' => SystemUserType::EXHIBITOR,
            'password' => '12345678',
            'email_verified_at' => now(),
        ]);
    }
}
