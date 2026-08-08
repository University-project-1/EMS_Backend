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

class BoothApprovedNotification extends Notification implements ShouldQueue, FcmNotification
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public function __construct(
        public readonly BoothRequest $boothRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->boothRequest->loadMissing('booth');
        $booth = $this->boothRequest->booth;

        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $actionUrl = "{$frontendUrl}/dashboard/booths/{$booth->id}";
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

    public function toFcm(object $notifiable): array { 
        return [ 
            'notification' => [ 
                'title' => __('notifications.booth_approved_title'), 
                'body' => __('notifications.booth_approved_body'), 
                ], 
                'data' => [ 
                    'type' => 'booth_approved', 
                    'target_id' => (string) $this->boothRequest->id, 
                ], 
            ]; 
    }
}
