<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class EventPaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Required for Your Event')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your event request for \"{$this->event->title}\" has been partially approved.")
            ->line('Please visit the administration office to complete the payment process and confirm your event.')
            ->salutation('Best regards, System Management Team');
    }
}
