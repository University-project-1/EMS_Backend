<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class EventApprovedNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly Event $event
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $actionUrl = "{$frontendUrl}/events/{$this->event->id}";

        return (new MailMessage)
            ->subject('Event Approved')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('We are pleased to inform you that your event "'.$this->event->title.'" has been approved successfully.')
            ->action('View Event Details', $actionUrl)
            ->line('We look forward to seeing you at the event!')
            ->salutation('Best regards, System Management Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'event_approved',
            'title' => 'notifications.event_approved_title',
            'body' => 'notifications.event_approved_body',
            'target_id' => $this->event->id,
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __('notifications.event_approved_title'),
                'body' => __('notifications.event_approved_body', ['title' => $this->event->title]),
            ],
            'data' => [
                'type' => 'event_approved',
                'target_id' => (string) $this->event->id,
            ],
        ];
    }
}
