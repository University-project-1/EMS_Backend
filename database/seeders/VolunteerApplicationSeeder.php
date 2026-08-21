<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Enum\SystemUserType;
use App\Models\SystemUser;
use App\Models\VolunteerApplication;
use Illuminate\Database\Seeder;

class VolunteerApplicationSeeder extends Seeder
{
    private const PENDING_COUNT = 24;

    private const APPROVED_COUNT = 14;

    private const REJECTED_COUNT = 10;

    public function run(): void
    {
        $reviewers = SystemUser::query()
            ->where('type', SystemUserType::ADMIN)
            ->orderBy('id')
            ->get();

        $statuses = [
            ...array_fill(0, self::PENDING_COUNT, Status::PENDING),
            ...array_fill(0, self::APPROVED_COUNT, Status::APPROVED),
            ...array_fill(0, self::REJECTED_COUNT, Status::REJECTED),
        ];

        foreach ($statuses as $index => $status) {
            $number = $index + 1;
            $reviewer = $status === Status::PENDING || $reviewers->isEmpty()
                ? null
                : $reviewers->get($index % $reviewers->count());

            VolunteerApplication::query()->updateOrCreate(
                ['email' => sprintf('volunteer.%03d@example.test', $number)],
                [
                    'full_name' => fake('en_US')->name(),
                    'phone' => sprintf('+9639%08d', $number),
                    'motivation' => fake('en_US')->realTextBetween(120, 220),
                    'education_or_occupation' => fake('en_US')->randomElement([
                        'Computer Engineering student.',
                        'Business Administration graduate.',
                        'Public Relations specialist.',
                        'Freelance graphic designer.',
                        'Media and Communications student.',
                    ]),
                    'skills' => fake('en_US')->randomElement([
                        'Visitor communication, event organization, and time management.',
                        'English language, customer service, and on-site coordination.',
                        'Photography, content management, and teamwork.',
                        'Registration, reception, problem solving, and effective communication.',
                    ]),
                    'city' => fake('en_US')->randomElement(['Damascus', 'Rural Damascus', 'Homs', 'Aleppo', 'Latakia']),
                    'privacy_consent_at' => now()->subDays($number),
                    'status' => $status,
                    'reviewed_by' => $reviewer?->getKey(),
                    'reviewed_at' => $reviewer ? now()->subDays(max(1, $number - 3)) : null,
                    'review_note' => $reviewer ? fake('en_US')->randomElement([
                        'The application was reviewed and all required details were provided.',
                        'The profile matches the current event coordination needs.',
                        'The decision was made after reviewing the application details.',
                    ]) : null,
                    'whatsapp_notification_sent_at' => $status === Status::APPROVED ? now()->subDays(max(1, $number - 2)) : null,
                    'whatsapp_notification_failed_at' => null,
                    'whatsapp_notification_error' => null,
                ]
            );
        }
    }
}
