<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Service;
use App\Models\SystemUser;
use App\Services\Shared\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LogicException;

class BoothRequestSeeder extends Seeder
{
    /**
     * @var array<string, array{company: string, system_user: string, attach_services: bool}>
     */
    private const APPROVED_BOOKINGS = [
        '2C-01' => ['company' => 'Dar Al feker', 'system_user' => 'Elcoach', 'attach_services' => true],
        '10D-01' => ['company' => 'GreenFoods Co.', 'system_user' => 'Elza3eem', 'attach_services' => false],
        '11F-01' => ['company' => 'Dar Al feker', 'system_user' => 'Elcoach', 'attach_services' => true],
        '25B-01' => ['company' => 'GreenFoods Co.', 'system_user' => 'Elza3eem', 'attach_services' => true],
        '25B-02' => ['company' => 'Metro Tech Labs', 'system_user' => 'Elza3eem', 'attach_services' => false],
        '26E-01' => ['company' => 'Al-Noor Publishing House', 'system_user' => 'Elcoach', 'attach_services' => true],
        '26E-02' => ['company' => 'Cedar Build Works', 'system_user' => 'Elza3eem', 'attach_services' => true],
        '36JD-01' => ['company' => 'Meridian Health Alliance', 'system_user' => 'Fawzy', 'attach_services' => true],
        '36JD-02' => ['company' => 'Atlas Commerce Hub', 'system_user' => 'Elcoach', 'attach_services' => false],
        '8K-01' => ['company' => 'SkyPoint Tourism Ventures', 'system_user' => 'Elza3eem', 'attach_services' => true],
    ];

    public function run(): void
    {
        $availableServices = Service::query()->orderBy('id')->take(5)->get();
        $boothNumbers = [
            ...array_keys(self::APPROVED_BOOKINGS),
            '2C-02',
            '10D-02',
        ];
        $booths = Booth::query()->whereIn('number', $boothNumbers)->get()->keyBy('number');
        $companies = Company::query()->get()->keyBy('name');
        $systemUsers = SystemUser::query()->get()->keyBy('name');

        DB::transaction(function () use ($availableServices, $booths, $companies, $systemUsers): void {
            BoothRequest::query()
                ->whereIn('booth_id', $booths->pluck('id'))
                ->delete();

            foreach (self::APPROVED_BOOKINGS as $boothNumber => $booking) {
                $booth = $booths->get($boothNumber);
                $company = $companies->get($booking['company']);
                $systemUser = $systemUsers->get($booking['system_user']);

                if (! $booth instanceof Booth || ! $company instanceof Company || ! $systemUser instanceof SystemUser) {
                    throw new LogicException("Missing approved booking dependency for booth {$boothNumber}.");
                }

                $company->update(['status' => Status::APPROVED]);
                $boothRequest = BoothRequest::query()->create([
                    'booth_id' => $booth->id,
                    'company_id' => $company->id,
                    'system_user_id' => $systemUser->id,
                    'final_price' => $booth->price,
                    'status' => Status::APPROVED,
                    'reason_for_booking' => "Approved booth booking for {$company->name}.",
                ]);

                $this->attachServices($boothRequest, $availableServices, $booking['attach_services']);
                $this->syncApprovedBooking($booth, $company, $systemUser);
            }

            $this->seedUnapprovedRequest(
                $booths->get('2C-02'),
                $companies->get('North Star Events'),
                $systemUsers->get('Elcoach'),
                Status::PENDING,
                'Pending booth request for North Star Events.',
            );
            $this->seedUnapprovedRequest(
                $booths->get('10D-02'),
                $companies->get('Artisan Market House'),
                $systemUsers->get('Elcoach'),
                Status::REJECTED,
                'Rejected booth request for Artisan Market House.',
            );
        });
    }

    private function attachServices(BoothRequest $boothRequest, $availableServices, bool $shouldAttach): void
    {
        if (! $shouldAttach || $availableServices->isEmpty()) {
            return;
        }

        $services = $availableServices->take(2);
        $totalServicesPrice = 0;

        foreach ($services as $service) {
            $quantity = 1;
            $unitPrice = $service->price ?? 0;

            $boothRequest->services()->create([
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);

            $totalServicesPrice += $unitPrice * $quantity;
        }

        $boothRequest->update([
            'final_price' => $boothRequest->booth->price + $totalServicesPrice,
        ]);
    }

    private function syncApprovedBooking(Booth $booth, Company $company, SystemUser $systemUser): void
    {
        $token = 'B-SEED-'.$booth->number;

        $booth->update([
            'company_id' => $company->id,
            'qr_token' => $token,
        ]);
        $booth->systemUsers()->syncWithoutDetaching([
            $systemUser->id => ['assigned_by' => null, 'created_at' => now()],
        ]);
        $booth->clearMediaCollection('qr_code');
        Storage::disk('public')->deleteDirectory('booths/'.$booth->id.'/qr_code');
        $booth->addMediaFromString(app(QrCodeService::class)->generateSvg($token))
            ->usingFileName("{$token}.svg")
            ->toMediaCollection('qr_code');
    }

    private function seedUnapprovedRequest(
        ?Booth $booth,
        ?Company $company,
        ?SystemUser $systemUser,
        Status $status,
        string $reasonForBooking,
    ): void {
        if (! $booth instanceof Booth || ! $company instanceof Company || ! $systemUser instanceof SystemUser) {
            throw new LogicException('Missing dependency for unapproved booth request seeding.');
        }

        $booth->update([
            'company_id' => null,
            'qr_token' => null,
        ]);
        $booth->clearMediaCollection('qr_code');
        Storage::disk('public')->deleteDirectory('booths/'.$booth->id.'/qr_code');

        BoothRequest::query()->create([
            'booth_id' => $booth->id,
            'company_id' => $company->id,
            'system_user_id' => $systemUser->id,
            'final_price' => $booth->price,
            'status' => $status,
            'reason_for_booking' => $reasonForBooking,
        ]);
    }
}
