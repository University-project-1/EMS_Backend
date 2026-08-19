<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Models\BoothRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class BoothPaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly BoothRequest $boothRequest) {}

    public function via(object $notifiable): array
    {
        // return ['database', 'mail'];
        return ['mail'];
    }

    // public function toDatabase(object $notifiable): array
    // {
        // return [
        //     'type' => 'booth_payment_reminder',
        //     'title' => 'Payment required for your booth booking',
        //     'body' => 'Please visit the administration office to complete your booth payment.',
        //     'target_id' => $this->boothRequest->getKey(),
        // ];
    // }

    public function toMail(object $notifiable): MailMessage
    {
        $this->boothRequest->loadMissing('booth');
        $boothNumber = $this->boothRequest->booth?->number ?? '#'.$this->boothRequest->booth_id;
        $boothUrl = config('app.frontend_url')."/dashboard/booths/{$this->boothRequest->booth_id}";

        return (new MailMessage)
            ->subject('Payment Required for Your Booth Booking')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("Your booth booking request for booth {$boothNumber} has been partially approved.")
            ->line('Please visit the administration office to complete the payment process and confirm your booking.')
            ->salutation('Best regards, System Management Team');
    }
}
