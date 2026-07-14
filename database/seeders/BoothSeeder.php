<?php

namespace Database\Seeders;

use App\Models\Booth;
use App\Models\Company;
use App\Models\Hall;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoothSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halls = Hall::all();
        $companies = Company::all();
        $elcoach = SystemUser::where('name', 'Elcoach')->firstOrFail();
        $fawzy = SystemUser::where('name', 'Fawzy')->firstOrFail();
        $elza3eem = SystemUser::where('name', 'Elza3eem')->firstOrFail();

        $boothA01 = Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-01',
            'svg_id' => 'booth-a01',
            'area' => 9.0,
            'price' => 250.00,
        ]);

        $boothA02 = Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-02',
            'svg_id' => 'booth-a02',
            'area' => 12.0,
            'price' => 300.00,
        ]);

        $boothA03 = Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'A-03',
            'svg_id' => 'booth-a03',
            'area' => 6.0,
            'price' => 180.00,
        ]);

        $boothB01 = Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => $companies->random()->id,
            'qr_token' => (string) Str::uuid(),
            'number' => 'B-01',
            'svg_id' => 'booth-b01',
            'area' => 8.5,
            'price' => 200.00,
        ]);

        Booth::create([
            'hall_id' => $halls->random()->id,
            'company_id' => null,
            'qr_token' => null,
            'number' => 'B-02',
            'svg_id' => 'booth-b02',
            'area' => 7.0,
            'price' => 160.00,
        ]);

        DB::table('booth_system_users')->insert([
            [
                'booth_id' => $boothA01->id,
                'system_user_id' => $elcoach->id,
                'assigned_by' => $fawzy->id,
                'created_at' => now(),
            ],
            [
                'booth_id' => $boothA02->id,
                'system_user_id' => $elza3eem->id,
                'assigned_by' => $fawzy->id,
                'created_at' => now(),
            ],
            [
                'booth_id' => $boothA03->id,
                'system_user_id' => $elcoach->id,
                'assigned_by' => $fawzy->id,
                'created_at' => now(),
            ],
            [
                'booth_id' => $boothB01->id,
                'system_user_id' => $fawzy->id,
                'assigned_by' => null,
                'created_at' => now(),
            ],
        ]);
    }
}
