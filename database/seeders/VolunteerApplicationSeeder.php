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
                    'full_name' => fake('ar_SA')->name(),
                    'phone' => sprintf('+9639%08d', $number),
                    'motivation' => fake('ar_SA')->realTextBetween(120, 220),
                    'education_or_occupation' => fake('ar_SA')->randomElement([
                        'طالب هندسة معلوماتية.',
                        'خريج إدارة أعمال.',
                        'موظف في مجال العلاقات العامة.',
                        'مصمم جرافيك مستقل.',
                        'طالب إعلام واتصال.',
                    ]),
                    'skills' => fake('ar_SA')->randomElement([
                        'التواصل مع الزوار، التنظيم، وإدارة الوقت.',
                        'اللغة الإنجليزية، خدمة العملاء، والتنسيق الميداني.',
                        'التصوير، إدارة المحتوى، والعمل ضمن فريق.',
                        'التسجيل والاستقبال، حل المشكلات، والتواصل الفعال.',
                    ]),
                    'city' => fake('ar_SA')->randomElement(['دمشق', 'ريف دمشق', 'حمص', 'حلب', 'اللاذقية']),
                    'privacy_consent_at' => now()->subDays($number),
                    'status' => $status,
                    'reviewed_by' => $reviewer?->getKey(),
                    'reviewed_at' => $reviewer ? now()->subDays(max(1, $number - 3)) : null,
                    'review_note' => $reviewer ? fake('ar_SA')->randomElement([
                        'تمت مراجعة الطلب واستكمال البيانات المطلوبة.',
                        'الملف مناسب للاحتياج التنظيمي الحالي.',
                        'تم اتخاذ القرار بعد مراجعة بيانات التقديم.',
                    ]) : null,
                    'whatsapp_notification_sent_at' => $status === Status::APPROVED ? now()->subDays(max(1, $number - 2)) : null,
                    'whatsapp_notification_failed_at' => null,
                    'whatsapp_notification_error' => null,
                ]
            );
        }
    }
}
