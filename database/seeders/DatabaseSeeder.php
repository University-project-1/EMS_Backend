<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
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
    
    }
}
