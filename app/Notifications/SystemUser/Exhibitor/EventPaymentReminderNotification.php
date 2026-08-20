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

class EventPaymentReminderNotification extends Notification implements FcmNotification, ShouldQueue
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
        return 'event_payment_reminder';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'event_payment_reminder',
            'title' => 'notifications.event_payment_reminder_title',
            'body' => 'notifications.event_payment_reminder_body',
            'target_id' => $this->event->getKey(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url')."/dashboard/events/{$this->event->getKey()}";

        return (new MailMessage)
            ->subject('Payment Required for Your Event')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your event request for \"{$this->event->title}\" has been partially approved and requires payment.")
            ->line('Please complete the payment process to confirm your event.')
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
                'type' => 'event_payment_reminder',
                'target_id' => (string) $this->event->getKey(),
            ],
        ];
    }

    private function key(string $suffix): string
    {
        return 'notifications.event_payment_reminder_'.$suffix;
    }
}
