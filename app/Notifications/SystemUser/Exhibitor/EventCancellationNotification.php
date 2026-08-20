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

class EventCancellationNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function databaseType(object $notifiable): string
    {
        return 'event_canceled';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'event_canceled',
            'title' => 'notifications.event_canceled_title',
            'body' => 'notifications.event_canceled_body',
            'target_id' => $this->event->getKey(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url')."/dashboard/events/{$this->event->getKey()}";

        return (new MailMessage)
            ->subject('Event Canceled')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your event \"{$this->event->title}\" has been canceled.")
            ->line('The event hall is now available for another booking.')
            ->action('View Event Details', $url)
            ->salutation('Best regards, System Management Team');
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __($this->key('title')),
                'body' => __($this->key('body'), ['title' => $this->event->title]),
            ],
            'data' => [
                'type' => 'event_canceled',
                'target_id' => (string) $this->event->getKey(),
            ],
        ];
    }

    private function key(string $suffix): string
    {
        return 'notifications.event_canceled_'.$suffix;
    }
}
