<?php

namespace App\Notifications\SystemUser;

use App\Models\BoothRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BoothApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly BoothRequest $boothRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->boothRequest->loadMissing('booth');
        $booth = $this->boothRequest->booth;

        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $actionUrl = "{$frontendUrl}/booths/{$booth->id}?token={$booth->qr_token}";

        return (new MailMessage)
            ->subject('Booth Booking Confirmation - Booth Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We are pleased to inform you that your booth booking request has been approved successfully.')
            ->line('Your booth number is: ' . $booth->id)
            ->line('Please keep this email, and use the button below to access your booth dashboard.')
            ->action('View Booth Details', $actionUrl)
            ->line('We look forward to seeing you at the exhibition!')
            ->salutation('Best regards, System Management Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booth_approved',
            'title' => 'notifications.booth_approved_title',
            'body' => 'notifications.booth_approved_body',
            'target_id' => $this->boothRequest->id,
        ];
    }
}
