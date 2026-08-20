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
        $wasem = User::create([
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

        $memo = User::create([
            'first_name' => 'Mehyar',
            'last_name' => 'Kheder',
            'email' => 'mehyarkhuder11e@gmail.com',
            'phone' => '+963968378834',
            'location' => 'Syria,Jaramana',
            'job' => 'Software Engineer',
            'gender' => 'male',
            'password' => '12345678',
            'birthday' => '2005-11-26',
        ]);

        $ZN = User::create([
            'first_name' => 'Mohamad',
            'last_name' => 'Zealnoun',
            'email' => 'mzyalnoun@gmail.com',
            'phone' => '+963935367608',
            'location' => 'Syria,Damascus',
            'job' => 'Software Engineer',
            'gender' => 'male',
            'password' => '12345678',
            'birthday' => '2006-02-06',
        ]);

        $fawzy = SystemUser::create([
            'name' => 'Fawzy',
            'email' => 'fawzy.sukkar2005@gmail.com',
            'password' => '12345678',
            'type' => SystemUserType::ADMIN,
            'email_verified_at' => now(),
        ]);

        $elza3eem = SystemUser::create([
            'name' => 'Elza3eem',
            'email' => 'abdalrahmansalloum200@gmail.com',
            'type' => SystemUserType::ADMIN,
            'password' => '12345678',
            'email_verified_at' => now(),
        ]);

        $elcoach = SystemUser::create([
            'name' => 'Elcoach',
            'email' => 'zuheiralhomsi73@gmail.com',
            'type' => SystemUserType::EXHIBITOR,
            'password' => '12345678',
            'email_verified_at' => now(),
        ]);

        $fawzyAvatar = database_path('assets/fawzy.jpg');
        if (is_file($fawzyAvatar)) {
            $fawzy->copyMedia($fawzyAvatar)->toMediaCollection('avatar');
        }

        $elza3eemAvatar = database_path('assets/Elza3em.png');
        if (is_file($elza3eemAvatar)) {
            $elza3eem->copyMedia($elza3eemAvatar)->toMediaCollection('avatar');
        }

        $elcoachAvatar = database_path('assets/RGBs.jpg');
        if (is_file($elcoachAvatar)) {
            $elcoach->copyMedia($elcoachAvatar)->toMediaCollection('avatar');
        }

        $wasemAvatar = database_path('assets/wasem.png');
        if(is_file($wasemAvatar)){
            $wasem->copyMedia($wasemAvatar)->toMediaCollection('user-avatars');
        }

        $ZNAvatar = database_path('assets/zealnoun.png');
        if(is_file($ZNAvatar)){
            $ZN->copyMedia($ZNAvatar)->toMediaCollection('user-avatars');
        }
    }
}
