<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\SystemUser;
use App\Models\Service;
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

        $availableServices = Service::inRandomOrder()->take(5)->get();

        $requests = [
            [
                'booth' => $booth25B01,
                'system_user_id' => $fawzy->id,
                'status' => Status::APPROVED,
                'company_id' => $summitRetailGroup->id,
                'reason_for_booking' => 'Approved booth request for 25B-01 from Summit Retail Group.',
                'attach_services' => true,
            ],
            [
                'booth' => $booth25B01,
                'system_user_id' => $elcoach->id,
                'status' => Status::PENDING,
                'company_id' => $northStarEvents->id,
                'reason_for_booking' => 'Pending booth request for 25B-01 from North Star Events.',
                'attach_services' => false,
            ],
            [
                'booth' => $booth25B01,
                'system_user_id' => $elza3eem->id,
                'status' => Status::PENDING,
                'company_id' => $metroTechLabs->id,
                'reason_for_booking' => 'Pending booth request for 25B-01 from Metro Tech Labs.',
                'attach_services' => true,
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $elcoach->id,
                'status' => Status::PENDING,
                'company_id' => $artisanMarketHouse->id,
                'reason_for_booking' => 'Pending booth request for 2C-01 from Artisan Market House.',
                'attach_services' => true,
            ],
            [
                'booth' => Booth::where('number', '10D-01')->firstOrFail(),
                'system_user_id' => $fawzy->id,
                'status' => Status::REJECTED,
                'company_id' => $greenFoods->id,
                'reason_for_booking' => 'Rejected booth request for 10D-01 from GreenFoods Co.',
                'attach_services' => false,
            ],
            [
                'booth' => Booth::where('number', '11F-01')->firstOrFail(),
                'system_user_id' => $elza3eem->id,
                'status' => Status::PENDING,
                'company_id' => $greenFoods->id,
                'reason_for_booking' => 'Pending booth request for 11F-01 from GreenFoods Co.',
                'attach_services' => true,
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $elcoach->id,
                'status' => Status::APPROVED,
                'company_id' => $northStarEvents->id,
                'reason_for_booking' => 'Approved booth request for 2C-01 from North Star Events.',
                'attach_services' => true,
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $fawzy->id,
                'status' => Status::PENDING,
                'company_id' => $artisanMarketHouse->id,
                'reason_for_booking' => 'Pending booth request for 2C-01 from Artisan Market House.',
                'attach_services' => false,
            ],
            [
                'booth' => Booth::where('number', '2C-01')->firstOrFail(),
                'system_user_id' => $elza3eem->id,
                'status' => Status::PENDING,
                'company_id' => $metroTechLabs->id,
                'reason_for_booking' => 'Pending booth request for 2C-01 from Metro Tech Labs.',
                'attach_services' => false,
            ],
            [
                'booth' => Booth::where('number', '10D-01')->firstOrFail(),
                'system_user_id' => $elza3eem->id,
                'status' => Status::APPROVED,
                'company_id' => $greenFoods->id,
                'reason_for_booking' => 'NOT Rejected booth request for 10D-01 from GreenFoods Co.',
                'attach_services' => false,
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
                ]
            );

            if ($boothRequest->trashed()) {
                $boothRequest->restore();
            }

            if ($requestData['attach_services'] && $availableServices->isNotEmpty()) {
                $boothRequest->services()->delete();

                $totalServicesPrice = 0;
                $servicesToAttach = $availableServices->random(rand(1, 3));

                foreach ($servicesToAttach as $service) {
                    $quantity = rand(1, 5);
                    $unitPrice = $service->price ?? 100;

                    $boothRequest->services()->create([
                        'service_id' => $service->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                    ]);

                    $totalServicesPrice += ($unitPrice * $quantity);
                }

                $boothRequest->update([
                    'final_price' => $requestData['booth']->price + $totalServicesPrice
                ]);
            }
        }
    }
}
