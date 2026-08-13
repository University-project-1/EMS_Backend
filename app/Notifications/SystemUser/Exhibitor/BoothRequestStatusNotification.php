<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
use App\Enum\Status;
use App\Interfaces\FcmNotification;
use App\Models\BoothRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class BoothRequestStatusNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly BoothRequest $boothRequest, public readonly Status $status)
    {
        if ($status === Status::PENDING) {
            throw new InvalidArgumentException('Booth request status notifications only support approved or rejected states.');
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booth_'.$this->status->value,
            'title' => $this->key('title'),
            'body' => $this->key('body'),
            'target_id' => $this->boothRequest->getKey(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->boothRequest->loadMissing('booth');
        $number = $this->boothRequest->booth?->number ?? '#'.$this->boothRequest->booth_id;
        $url = config('app.frontend_url')."/dashboard/booths/{$this->boothRequest->booth_id}";
        $approved = $this->status === Status::APPROVED;

        return (new MailMessage)
            ->subject($approved ? 'Booth Booking Confirmation - Booth Approved' : 'Booth Booking Request Update - Rejected')
            ->greeting('Hello '.$notifiable->name.',')
            ->line($approved ? "We are pleased to inform you that your booth booking request for booth {$number} has been approved successfully." : "We regret to inform you that your booth booking request for booth {$number} has been rejected.")
            ->line($approved ? 'You can now access your booth dashboard and prepare for the exhibition.' : 'You can review the request details and submit a new request when appropriate.')
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
                'type' => 'booth_'.$this->status->value,
                'target_id' => (string) $this->boothRequest->getKey(),
            ],
        ];
    }

    private function key(string $suffix): string
    {
        return 'notifications.booth_'.$this->status->value.'_'.$suffix;
    }
}
