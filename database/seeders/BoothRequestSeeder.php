<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
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

        $booth25B01 = Booth::where('number', '25B-01')->firstOrFail();

        $northStarEvents = Company::where('name', 'North Star Events')->firstOrFail();
        $artisanMarketHouse = Company::where('name', 'Artisan Market House')->firstOrFail();
        $metroTechLabs = Company::where('name', 'Metro Tech Labs')->firstOrFail();
        $summitRetailGroup = Company::where('name', 'Summit Retail Group')->firstOrFail();
        $greenFoods = Company::where('name', 'GreenFoods Co.')->firstOrFail();

        $requests = [
            [
                'booth' => $booth25B01,
                'system_user_id' => $fawzy->id,
                'status' => Status::APPROVED,
                'company_id' => $summitRetailGroup->id,
                'reason_for_booking' => 'Approved booth request for 25B-01 from Summit Retail Group.',
            ],
            [
                'booth' => $booth25B01,
                'system_user_id' => $elcoach->id,
                'status' => Status::PENDING,
                'company_id' => $northStarEvents->id,
                'reason_for_booking' => 'Pending booth request for 25B-01 from North Star Events.',
            ],
            [
                'booth' => $booth25B01,
                'system_user_id' => $elza3eem->id,
                'status' => Status::PENDING,
                'company_id' => $metroTechLabs->id,
                'reason_for_booking' => 'Pending booth request for 25B-01 from Metro Tech Labs.',
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $elcoach->id,
                'status' => Status::PENDING,
                'company_id' => $artisanMarketHouse->id,
                'reason_for_booking' => 'Pending booth request for 2C-01 from Artisan Market House.',
            ],
            [
                'booth' => Booth::where('number', '10D-01')->firstOrFail(),
                'system_user_id' => $fawzy->id,
                'status' => Status::REJECTED,
                'company_id' => $greenFoods->id,
                'reason_for_booking' => 'Rejected booth request for 10D-01 from GreenFoods Co.',
            ],
            [
                'booth' => Booth::where('number', '11F-01')->firstOrFail(),
                'system_user_id' => $elza3eem->id,
                'status' => Status::PENDING,
                'company_id' => $greenFoods->id,
                'reason_for_booking' => 'Pending booth request for 11F-01 from GreenFoods Co.',
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $elcoach->id,
                'status' => Status::APPROVED,
                'company_id' => $northStarEvents->id,
                'reason_for_booking' => 'Approved booth request for 2C-01 from North Star Events.',
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $fawzy->id,
                'status' => Status::PENDING,
                'company_id' => $artisanMarketHouse->id,
                'reason_for_booking' => 'Pending booth request for 2C-01 from Artisan Market House.',
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $elza3eem->id,
                'status' => Status::PENDING,
                'company_id' => $metroTechLabs->id,
                'reason_for_booking' => 'Pending booth request for 2C-01 from Metro Tech Labs.',
            ],
            [
                'booth' => Booth::where('number', '10D-01')->firstOrFail(),
                'system_user_id' => $elza3eem->id,
                'status' => Status::REJECTED,
                'company_id' => $greenFoods->id,
                'reason_for_booking' => 'Rejected booth request for 10D-01 from GreenFoods Co.',
            ],
        ];

        foreach ($requests as $requestData) {
            $boothRequest = BoothRequest::withTrashed()->updateOrCreate(
                [
                    'booth_id' => $requestData['booth']->id,
                    'system_user_id' => $requestData['system_user_id'],
                ],
                [
                    'company_id' => $requestData['company_id'],
                    'final_price' => $requestData['booth']->price,
                    'status' => $requestData['status'],
                    'reason_for_booking' => $requestData['reason_for_booking'],
                ],
            );

            if ($boothRequest->trashed()) {
                $boothRequest->restore();
            }
        }
    }
}
