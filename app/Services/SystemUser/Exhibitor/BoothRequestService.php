<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\BoothRequestDTO;
use App\DTOs\SystemUser\CompanyDTO;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Service;
use App\Models\SystemUser;
use Illuminate\Support\Facades\DB;

class BoothRequestService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly CompanyService $companyService,
    ){}

    public function confirmBoothBooking(SystemUser $user, BoothRequestDTO $bookingDTO, ?CompanyDTO $companyDTO){
        return DB::transaction(function() use ($user, $bookingDTO, $companyDTO){
            Service::lockForUpdate();
            $booth = Booth::findOrFail($bookingDTO->boothId);
            $company = $bookingDTO->companyId ? Company::findOrFail($bookingDTO->companyId)
            : $this->companyService->create($user, $companyDTO);

            $boothRequest = BoothRequest::create([
                'system_user_id' => $user->id,
                'booth_id' => $booth->id,
                'company_id' => $company->id,
                'reason_for_booking' =>$bookingDTO->reasonForBooking,
                'status' => Status::PENDING,
                'final_price' => 0,
            ]);
            $servicesCost = $bookingDTO->services ? $this->attachServices($boothRequest, $bookingDTO->services) : 0 ;

            $boothRequest->update(['final_price' => $booth->price + $servicesCost]);

            return $boothRequest->load(['services', 'company.logoMedia', 'company.galleryMedia']);;
        });
    }

    private function attachServices(BoothRequest $boothRequest, array $services): float
    {
        $cost = 0;
        $servicesToInsert = [];

        $serviceIds = array_column($services, 'service_id');
        $dbPrices = Service::whereIn('id', $serviceIds)->lockForUpdate()->pluck('price', 'id');

        foreach ($services as $service) {
            $serviceId = $service['service_id'];
            $quantity = $service['quantity'];
            $unitPrice = $dbPrices[$serviceId];

            $cost += ($unitPrice * $quantity);

            $servicesToInsert[] = [
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ];
        }

        $boothRequest->services()->createMany($servicesToInsert);

        return $cost;
    }
}
