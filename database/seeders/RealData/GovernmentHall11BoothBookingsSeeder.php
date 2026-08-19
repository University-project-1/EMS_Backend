<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Service;
use App\Models\SystemUser;
use App\Services\Shared\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class GovernmentHall11BoothBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $governmentCompanyIds = [];
        foreach (GovernmentHall11Data::companies() as $definition) {
            $governmentCompanyIds[] = Company::query()->where('name', $definition['name'])->firstOrFail()->id;
        }
        $legacyBooths = Booth::query()->where('hall_id', 15)->whereNotNull('company_id')->whereNotIn('company_id', $governmentCompanyIds)->get();
        foreach ($legacyBooths as $legacyBooth) {
            BoothRequest::query()->where('booth_id', $legacyBooth->id)->delete();
            DB::table('booth_system_users')->where('booth_id', $legacyBooth->id)->delete();
            $legacyBooth->clearMediaCollection('qr_code');
            $legacyBooth->update(['company_id' => null, 'qr_token' => null]);
        }
        $available = Booth::query()->where('hall_id', 15)->whereNull('company_id')->orderBy('id')->get()->values();
        $services = Service::query()->orderBy('id')->limit(3)->get();
        $fallbackUser = SystemUser::query()->where('email', 'mohammed.fawzy.sukkar@88ninety.test')->first() ?? SystemUser::query()->orderBy('id')->firstOrFail();
        $index = 0;

        foreach (GovernmentHall11Data::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $booth = Booth::query()->where('hall_id', 15)->where('company_id', $company->id)->first();
            if (! $booth) {
                $booth = $available->get($index++);
            }
            if (! $booth) {
                throw new RuntimeException('Hall 11 does not have enough existing unassigned booths for the government scenario.');
            }

            $booth->update(['company_id' => $company->id]);
            $person = collect(GovernmentHall11Data::people())->first(fn (array $candidate): bool => in_array($slug, $candidate['companies'], true));
            $requester = $person
                ? SystemUser::query()->where('email', $person['email'])->firstOrFail()
                : $fallbackUser;
            $request = BoothRequest::query()->updateOrCreate(
                ['company_id' => $company->id, 'booth_id' => $booth->id],
                [
                    'system_user_id' => $requester->id,
                    'status' => Status::APPROVED->value,
                    'reason_for_booking' => 'Syrian public-sector Hall 11 participation request; source provenance is recorded in GovernmentHall11Data.',
                    'final_price' => $booth->price ?? 0,
                ],
            );

            $request->services()->delete();
            foreach ($services as $service) {
                $request->services()->create([
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'unit_price' => $service->price,
                ]);
            }

            if ($person) {
                $member = SystemUser::query()->where('email', $person['email'])->firstOrFail();
                DB::table('booth_system_users')->updateOrInsert(
                    ['booth_id' => $booth->id, 'system_user_id' => $member->id],
                    ['assigned_by' => null, 'created_at' => now()],
                );
            }
            $this->syncQr($booth);
        }
    }

    private function syncQr(Booth $booth): void
    {
        $token = 'B-'.$booth->id.'-'.Str::random(10);
        $booth->update(['qr_token' => $token]);
        $booth->clearMediaCollection('qr_code');
        \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('booths/'.$booth->id.'/qr_code');
        $booth->addMediaFromString(app(QrCodeService::class)->generateSvg($token))
            ->usingFileName($token.'.svg')
            ->toMediaCollection('qr_code');
    }
}
