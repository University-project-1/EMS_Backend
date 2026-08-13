<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\ReportDTO;
use App\Models\Booth;
use App\Models\Event;
use App\Notifications\SystemUser\Admin\NewReportNotification;
use App\Services\Shared\NotificationRecipientResolver;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ReportService
{
    public function __construct(private readonly NotificationRecipientResolver $notificationRecipients) {}

    public function store(ReportDTO $dto, Authenticatable $user)
    {
        $reportableType = $dto->event_id ? Event::class : Booth::class;
        $reportableId = $dto->event_id ?: $dto->booth_id;

        return DB::transaction(function () use ($dto, $user, $reportableType, $reportableId) {
            $report = $user->reports()->create([
                'reportable_type' => $reportableType,
                'reportable_id' => $reportableId,
                'title' => $dto->title,
                'description' => $dto->description,
            ]);

            DB::afterCommit(function () use ($report): void {
                Notification::send(
                    $this->notificationRecipients->admins(),
                    new NewReportNotification($report)
                );
            });

            return $report;
        });
    }
}
