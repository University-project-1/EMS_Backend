<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;

class BoothRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elcoach = SystemUser::where('name', 'Elcoach')->firstOrFail();
        $fawzy = SystemUser::where('name', 'Fawzy')->firstOrFail();
        $elza3eem = SystemUser::where('name', 'Elza3eem')->firstOrFail();

        $boothA01 = Booth::where('number', 'A-01')->firstOrFail();
        $boothA02 = Booth::where('number', 'A-02')->firstOrFail();
        $boothB01 = Booth::where('number', 'B-01')->firstOrFail();

        BoothRequest::create([
            'booth_id' => $boothA01->id,
            'company_id' => $boothA01->company_id,
            'system_user_id' => $elcoach->id,
            'final_price' => $boothA01->price,
            'status' => Status::APPROVED,
            'reason_for_booking' => 'Exhibitor booth request created for Elcoach.',
        ]);

        BoothRequest::create([
            'booth_id' => $boothA02->id,
            'company_id' => $boothA02->company_id,
            'system_user_id' => $fawzy->id,
            'final_price' => $boothA02->price,
            'status' => Status::APPROVED,
            'reason_for_booking' => 'Approved booth request for the team lead.',
        ]);

        BoothRequest::create([
            'booth_id' => $boothB01->id,
            'company_id' => $boothB01->company_id,
            'system_user_id' => $elza3eem->id,
            'final_price' => $boothB01->price,
            'status' => Status::PENDING,
            'reason_for_booking' => 'Pending review for a second booth allocation.',
        ]);
    }
}
