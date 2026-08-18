<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
use App\Enum\RequestRejectionReason;
use App\Enum\Status;
use App\Interfaces\FcmNotification;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class EventRequestStatusNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly Event $event,
        public readonly Status $status,
        public readonly ?RequestRejectionReason $rejectionReason = null,
    ) {
        if (! in_array($status, [Status::APPROVED, Status::REJECTED], true)) {
            throw new InvalidArgumentException('Event status notifications only support approved or rejected states.');
        }

        if ($status === Status::APPROVED && $rejectionReason !== null) {
            throw new InvalidArgumentException('Approved event notifications cannot include a rejection reason.');
        }

        if ($rejectionReason === RequestRejectionReason::BOOTH_CONFLICT) {
            throw new InvalidArgumentException('Booth conflict is not a valid event rejection reason.');
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
            'target_id' => $this->event->getKey(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url')."/dashboard/events/{$this->event->getKey()}";
        $approved = $this->status === Status::APPROVED;

        return (new MailMessage)
            ->subject($approved ? 'Event Request Approved' : 'Event Request Update - Rejected')
            ->greeting('Hello '.$notifiable->name.',')
            ->line($approved
                ? 'We are pleased to inform you that your event request for "'.$this->event->title.'" has been approved successfully.'
                : $this->rejectionMailLine())
            ->line($approved
                ? 'Your event is now available in the exhibition schedule.'
                : 'You can review the request details and submit a new request when appropriate.')
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
                'type' => $this->notificationType(),
                'target_id' => (string) $this->event->getKey(),
            ],
        ];
    }

    private function notificationType(): string
    {
        return $this->rejectionReason === null
            ? 'event_'.$this->status->value
            : 'event_'.$this->rejectionReason->value;
    }

    private function key(string $suffix): string
    {
        return $this->rejectionReason === null
            ? 'notifications.event_'.$this->status->value.'_'.$suffix
            : 'notifications.event_'.$this->rejectionReason->value.'_'.$suffix;
    }

    private function rejectionMailLine(): string
    {
        return match ($this->rejectionReason) {
            RequestRejectionReason::EVENT_EXPIRED => 'We regret to inform you that your event request for "'.$this->event->title.'" was automatically rejected because its start time passed before approval.',
            RequestRejectionReason::EVENT_SCHEDULE_CONFLICT => 'We regret to inform you that your event request for "'.$this->event->title.'" was automatically rejected because another event was approved for the same hall during an overlapping time period.',
            default => 'We regret to inform you that your event request for "'.$this->event->title.'" has been rejected.',
        };
    }
}
