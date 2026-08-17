<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class GovernmentHall11InvitationsSeeder extends Seeder
{
    public function run(): void
    {
        $sender = SystemUser::query()->orderBy('id')->firstOrFail();
        foreach (GovernmentHall11Data::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $person = collect(GovernmentHall11Data::people())->first(fn (array $candidate): bool => in_array($slug, $candidate['companies'], true));
            $email = $person['email'] ?? 'protocol.'.$slug.'@government-hall11.test';
            Invitation::query()->updateOrCreate(
                [
                    'inviteable_type' => Company::class,
                    'inviteable_id' => $company->id,
                    'email' => $email,
                ],
                [
                    'sender_id' => $sender->id,
                    'token' => 'GOV-H11-'.Str::upper(Str::random(12)),
                    'status' => Status::PENDING->value,
                    'expires_at' => now()->addDays(30),
                ],
            );
        }
    }
}
