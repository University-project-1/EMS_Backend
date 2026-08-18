<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\SystemUser;
use App\Services\Shared\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class RealTechBoothBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $available = Booth::query()->whereNull('company_id')->orderBy('id')->get()->values();
        $index = 0;

        foreach (RealTechData::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $booth = Booth::query()->where('company_id', $company->id)->first();
            if (! $booth) {
                $booth = $available->get($index++);
            }
            if (! $booth) {
                throw new RuntimeException('Not enough existing unassigned booths for the clean tech scenario; no booth was created.');
            }

            $booth->update(['company_id' => $company->id]);
            $person = collect(RealTechData::people())->firstWhere('company', $slug);
            $systemUser = $person
                ? SystemUser::query()->where('email', $person['email'])->firstOrFail()
                : SystemUser::query()->where('type', 'exhibitor')->orderBy('id')->firstOrFail();

            $boothRequest = BoothRequest::query()->updateOrCreate(
                ['company_id' => $company->id, 'booth_id' => $booth->id],
                [
                    'system_user_id' => $systemUser->id,
                    'status' => Status::APPROVED->value,
                    'reason_for_booking' => 'Clean Tech company booth booking scenario; source links are stored in the company and people Seeder comments.',
                    'final_price' => $booth->price ?? 0,
                ],
            );

            $this->syncApprovedBoothQr($boothRequest);

            foreach (collect(RealTechData::people())->where('company', $slug) as $member) {
                $memberUser = SystemUser::query()->where('email', $member['email'])->firstOrFail();
                DB::table('booth_system_users')->updateOrInsert(
                    ['booth_id' => $booth->id, 'system_user_id' => $memberUser->id],
                    ['assigned_by' => null, 'created_at' => now()],
                );
            }
        }

        // Keep every approved booth aligned with the same acceptance workflow.
        // Events are not touched because this query only targets booth_requests.
        foreach (BoothRequest::query()->where('status', Status::APPROVED->value)->whereNotNull('booth_id')->get() as $approvedRequest) {
            $this->syncApprovedBoothQr($approvedRequest);
        }
    }

    private function syncApprovedBoothQr(BoothRequest $boothRequest): void
    {
        $booth = Booth::query()->findOrFail($boothRequest->booth_id);
        $token = 'B-'.$boothRequest->booth_id.'-'.Str::random(10);

        $booth->update(['qr_token' => $token]);
        $booth->clearMediaCollection('qr_code');
        $booth->addMediaFromString(app(QrCodeService::class)->generateSvg($token))
            ->usingFileName("{$token}.svg")
            ->toMediaCollection('qr_code');
    }
}
