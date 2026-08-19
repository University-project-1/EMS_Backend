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

final class ExpandedTechBoothBookingsSeeder extends Seeder
{
    public function run(): void
    {
        $available = Booth::query()->whereNull('company_id')->orderBy('id')->get()->values();
        $index = 0;
        $people = collect(ExpandedTechData::people());

        foreach (ExpandedTechData::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $booth = Booth::query()->where('company_id', $company->id)->first();
            if (! $booth) {
                $booth = $available->get($index++);
            }
            if (! $booth) {
                throw new RuntimeException('Not enough existing unassigned booths; no booth was created.');
            }
            $booth->update(['company_id' => $company->id]);

            $person = $people->first(static fn (array $person): bool => in_array($slug, $person['companies'], true));
            if (! $person) {
                throw new RuntimeException("No canonical company representative found for {$slug}.");
            }
            $user = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            $request = BoothRequest::query()->updateOrCreate(
                ['company_id' => $company->id, 'booth_id' => $booth->id],
                ['system_user_id' => $user->id, 'status' => Status::APPROVED->value, 'reason_for_booking' => 'Expanded verified technology-company exhibition booking; source links are stored in ExpandedTechData.', 'final_price' => $booth->price ?? 0],
            );
            $this->syncQr($request);

            foreach ($people->filter(static fn (array $member): bool => in_array($slug, $member['companies'], true)) as $member) {
                $memberUser = SystemUser::query()->where('email', $member['email'])->firstOrFail();
                DB::table('booth_system_users')->updateOrInsert(
                    ['booth_id' => $booth->id, 'system_user_id' => $memberUser->id],
                    ['assigned_by' => null, 'created_at' => now()],
                );
            }
        }
    }

    private function syncQr(BoothRequest $request): void
    {
        $booth = Booth::query()->findOrFail($request->booth_id);
        $token = 'B-'.$request->booth_id.'-'.Str::random(10);
        $booth->update(['qr_token' => $token]);
        $booth->clearMediaCollection('qr_code');
        \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('booths/'.$booth->id.'/qr_code');
        $booth->addMediaFromString(app(QrCodeService::class)->generateSvg($token))->usingFileName($token.'.svg')->toMediaCollection('qr_code');
    }
}
