<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
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

    public function __construct(public readonly Event $event, public readonly Status $status)
    {
        if (! in_array($status, [Status::APPROVED, Status::REJECTED], true)) {
            throw new InvalidArgumentException('Event status notifications only support approved or rejected states.');
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'event_'.$this->status->value,
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
            ->line($approved ? 'We are pleased to inform you that your event request for "'.$this->event->title.'" has been approved successfully.' : 'We regret to inform you that your event request for "'.$this->event->title.'" has been rejected.')
            ->line($approved ? 'Your event is now available in the exhibition schedule.' : 'You can review the request details and submit a new request when appropriate.')
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
                'type' => 'event_'.$this->status->value,
                'target_id' => (string) $this->event->getKey(),
            ],
        ];
    }

    private function key(string $suffix): string
    {
        return 'notifications.event_'.$this->status->value.'_'.$suffix;
    }
}
