<?php

namespace App\Notifications\SystemUser\Shared;

use App\Channels\FcmChannel;
use App\Enum\AnnouncementNotificationAction;
use App\Interfaces\FcmNotification;
use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AnnouncementNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly Announcement $announcement,
        public readonly AnnouncementNotificationAction $action
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'announcement',
            'title' => $this->announcement->title,
            'body' => $this->announcement->description,
            'target_id' => $this->announcement->id,
            'action' => $this->action->value,
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => Str::limit($this->announcement->title, 200),
                'body' => Str::limit($this->announcement->description, 700),
            ],
            'data' => [
                'type' => 'announcement',
                'target_id' => (string) $this->announcement->id,
                'action' => $this->action->value,
            ],
        ];
    }
}
