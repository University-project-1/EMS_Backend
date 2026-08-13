<?php

namespace App\Notifications\SystemUser\Exhibitor;

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class NewReviewNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'review_created', 'title' => 'notifications.review_created_title',
            'body' => 'notifications.review_created_body',
            'target_id' => $this->review->getKey(),
            'reviewable_type' => $this->review->reviewable_type,
            'reviewable_id' => $this->review->reviewable_id,
            'rating' => $this->review->rating,
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __('notifications.review_created_title'),
                'body' => __('notifications.review_created_body', ['rating' => $this->review->rating]),
            ],
            'data' => [
                'type' => 'review_created',
                'target_id' => (string) $this->review->getKey(),
                'reviewable_type' => $this->review->reviewable_type,
                'reviewable_id' => (string) $this->review->reviewable_id,
                'rating' => (string) $this->review->rating,
            ],
        ];
    }
}
