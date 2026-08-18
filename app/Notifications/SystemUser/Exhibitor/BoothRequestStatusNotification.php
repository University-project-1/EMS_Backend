<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
use App\Enum\RequestRejectionReason;
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

    public function __construct(
        public readonly BoothRequest $boothRequest,
        public readonly Status $status,
        public readonly ?RequestRejectionReason $rejectionReason = null,
    ) {
        if ($status === Status::PENDING) {
            throw new InvalidArgumentException('Booth request status notifications only support approved or rejected states.');
        }

        if ($status === Status::APPROVED && $rejectionReason !== null) {
            throw new InvalidArgumentException('Approved booth notifications cannot include a rejection reason.');
        }

        if ($rejectionReason !== null && $rejectionReason !== RequestRejectionReason::BOOTH_CONFLICT) {
            throw new InvalidArgumentException('Only booth conflict is a valid booth rejection reason.');
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->notificationType(),
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
            ->line($approved
                ? "We are pleased to inform you that your booth booking request for booth {$number} has been approved successfully."
                : $this->rejectionMailLine($number))
            ->line($approved
                ? 'You can now access your booth dashboard and prepare for the exhibition.'
                : 'You can review the request details and submit a new request when appropriate.')
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
                'type' => $this->notificationType(),
                'target_id' => (string) $this->boothRequest->getKey(),
            ],
        ];
    }

    private function notificationType(): string
    {
        return $this->rejectionReason === null
            ? 'booth_'.$this->status->value
            : 'booth_'.$this->rejectionReason->value;
    }

    private function key(string $suffix): string
    {
        return $this->rejectionReason === null
            ? 'notifications.booth_'.$this->status->value.'_'.$suffix
            : 'notifications.booth_'.$this->rejectionReason->value.'_'.$suffix;
    }

    private function rejectionMailLine(string $number): string
    {
        return $this->rejectionReason === RequestRejectionReason::BOOTH_CONFLICT
            ? "We regret to inform you that your booth booking request for booth {$number} was automatically rejected because another request for the same booth was approved."
            : "We regret to inform you that your booth booking request for booth {$number} has been rejected.";
    }
}
