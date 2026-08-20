<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use App\Models\BoothRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class BoothPaymentReminderNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly BoothRequest $boothRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function databaseType(object $notifiable): string
    {
        return 'booth_payment_reminder';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booth_payment_reminder',
            'title' => 'notifications.booth_payment_reminder_title',
            'body' => 'notifications.booth_payment_reminder_body',
            'target_id' => $this->boothRequest->getKey(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->boothRequest->loadMissing('booth');
        $boothNumber = $this->boothRequest->booth?->number ?? '#'.$this->boothRequest->booth_id;
        $url = config('app.frontend_url')."/dashboard/booths/{$this->boothRequest->booth_id}";

        return (new MailMessage)
            ->subject('Payment Required for Your Booth Booking')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your booth booking request for booth {$boothNumber} has been partially approved and requires payment.")
            ->line('Please complete the payment process to confirm your booking.')
            ->action('View Booth Details', $url)
            ->salutation('Best regards, System Management Team');
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __($this->key('title')),
                'body' => __($this->key('body')),
            ],
            'data' => [
                'type' => 'booth_payment_reminder',
                'target_id' => (string) $this->boothRequest->getKey(),
            ],
        ];
    }

    private function key(string $suffix): string
    {
        return 'notifications.booth_payment_reminder_'.$suffix;
    }
}
