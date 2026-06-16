<?php

namespace Database\Seeders;

use App\Models\SystemUser;
use App\Models\User;
use App\Models\Company;
use App\Models\Hall;
use App\Models\Booth;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
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
        ]);

        SystemUser::create([
            'name' => 'Elza3eem',
            'email' => 'abdalrahmansalloum200@gmail.com',
            'password' => '12345678',
        ]);

        $companyA = Company::create([
            'name' => 'Dar Al feker',
            'business_sector' => 'Lectures & Exhibitions',
            'social_links' => ['website' => 'https://dar.com', 'linkedin' => 'https://linkedin.com/company/dar'],
            'phone' => '+963112223334',
            'year_founded' => 2015,
            'description' => 'Leading readers for reading.',
            'headquarters_lat' => 33.513807,
            'headquarters_lng' => 36.276528,
        ]);

        $companyB = Company::create([
            'name' => 'GreenFoods Co.',
            'business_sector' => 'Food & Beverage',
            'social_links' => ['website' => 'https://greenfoods.example', 'facebook' => 'https://facebook.com/greenfoods'],
            'phone' => '+963114445556',
            'year_founded' => 2008,
            'description' => 'Organic, sustainable food producer with regional distribution.',
            'headquarters_lat' => 33.500000,
            'headquarters_lng' => 36.300000,
        ]);

        $hallMain = Hall::create([
            'number' => 'Main-1',
            'area' => 1500.0,
            'svg_id' => 'hall-main-1',
            'type' => 'exhibition',
        ]);

        $hallB = Hall::create([
            'number' => 'Hall-B',
            'area' => 800.0,
            'svg_id' => 'hall-b',
            'type' => 'conference',
        ]);

        Booth::create([
            'hall_id' => $hallMain->id,
            'company_id' => $companyA->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-01',
            'svg_id' => 'booth-a01',
            'area' => 9.0,
            'price' => 250.00,
        ]);

        Booth::create([
            'hall_id' => $hallMain->id,
            'company_id' => $companyA->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-02',
            'svg_id' => 'booth-a02',
            'area' => 12.0,
            'price' => 300.00,
        ]);

        Booth::create([
            'hall_id' => $hallMain->id,
            'company_id' => $companyB->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-03',
            'svg_id' => 'booth-a03',
            'area' => 6.0,
            'price' => 180.00,
        ]);

        Booth::create([
            'hall_id' => $hallB->id,
            'company_id' => $companyB->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'B-01',
            'svg_id' => 'booth-b01',
            'area' => 8.5,
            'price' => 200.00,
        ]);

        Booth::create([
            'hall_id' => $hallB->id,
            'company_id' => null,
            'qr_token' => (string) Str::uuid(),
            'number' => 'B-02',
            'svg_id' => 'booth-b02',
            'area' => 7.0,
            'price' => 160.00,
        ]);
    }

}
