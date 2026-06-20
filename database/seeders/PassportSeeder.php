<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'System Access Client',
            '--provider' => 'system_users',
        ]);
        $this->command->info('Personal access client "System Access Client" created successfully.');

        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'System Access Client',
            '--provider' => 'users',
        ]);
        $this->command->info('Personal access client "User Access Client" created successfully.');
    }
}
