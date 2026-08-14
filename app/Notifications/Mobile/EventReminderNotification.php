<?php

namespace App\Notifications\Mobile;

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class EventReminderNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'event_reminder',
            'title' => 'notifications.event_reminder_title',
            'body' => 'notifications.event_reminder_body',
            'target_id' => $this->event->getKey(),
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __('notifications.event_reminder_title'),
                'body' => __('notifications.event_reminder_body', ['title' => $this->event->title]),
            ],
            'data' => [
                'type' => 'event_reminder',
                'target_id' => (string) $this->event->getKey(),
            ],
        ];
    }
}
