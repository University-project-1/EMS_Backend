<?php

namespace App\Notifications\Mobile;

use App\Channels\FcmChannel;
use App\Enum\LeadInterestNotificationType;
use App\Interfaces\FcmNotification;
use App\Models\Booth;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class LeadInterestNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly Booth|Event $content,
        public readonly LeadInterestNotificationType $type,
    ) {
        if (! $this->isSupportedCombination()) {
            throw new InvalidArgumentException('Unsupported lead-interest notification content and type combination.');
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type->value,
            'title' => $this->titleKey(),
            'body' => $this->bodyKey(),
            'target_id' => $this->content->getKey(),
            'target_type' => $this->content::class,
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __($this->titleKey()),
                'body' => __($this->bodyKey(), ['title' => $this->contentTitle()]),
            ],
            'data' => [
                'type' => $this->type->value,
                'target_id' => (string) $this->content->getKey(),
                'target_type' => $this->content::class,
            ],
        ];
    }

    private function isSupportedCombination(): bool
    {
        return match ($this->type) {
            LeadInterestNotificationType::COMPANY_BOOTH_CREATED => $this->content instanceof Booth,
            LeadInterestNotificationType::COMPANY_EVENT_CREATED, LeadInterestNotificationType::ORGANIZER_EVENT_CREATED => $this->content instanceof Event,
        };
    }

    private function titleKey(): string
    {
        return 'notifications.'.$this->type->value.'_title';
    }

    private function bodyKey(): string
    {
        return 'notifications.'.$this->type->value.'_body';
    }

    private function contentTitle(): string
    {
        return $this->content instanceof Event
            ? $this->content->title
            : __('notifications.booth_label', ['number' => $this->content->number]);
    }
}
