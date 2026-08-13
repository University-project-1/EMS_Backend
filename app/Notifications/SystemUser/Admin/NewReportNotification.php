<?php

namespace App\Notifications\SystemUser\Admin;

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class NewReportNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Report $report) {}

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'report_created',
            'title' => 'notifications.report_created_title',
            'body' => 'notifications.report_created_body',
            'target_id' => $this->report->getKey(),
            'reportable_type' => $this->report->reportable_type,
            'reportable_id' => $this->report->reportable_id,
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return ['notification' => [
            'title' => __('notifications.report_created_title'),
            'body' => __('notifications.report_created_body')],
            'data' => [
                'type' => 'report_created',
                'target_id' => (string) $this->report->getKey(),
                'reportable_type' => $this->report->reportable_type,
                'reportable_id' => (string) $this->report->reportable_id,
            ],
        ];
    }
}
