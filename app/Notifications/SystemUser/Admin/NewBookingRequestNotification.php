<?php

namespace App\Notifications\SystemUser\Admin;

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use App\Models\BoothRequest;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class NewBookingRequestNotification extends Notification implements FcmNotification, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public readonly Event|BoothRequest $bookingRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->notificationType(),
            'title' => 'notifications.booking_request_created_title',
            'body' => 'notifications.booking_request_created_body',
            'target_id' => $this->bookingRequest->getKey(),
            'request_type' => $this->requestType(),
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => __('notifications.booking_request_created_title'),
                'body' => __('notifications.booking_request_created_body'),
            ],
            'data' => [
                'type' => $this->notificationType(),
                'target_id' => (string) $this->bookingRequest->getKey(),
                'request_type' => $this->requestType(),
            ],
        ];
    }

    private function notificationType(): string
    {
        return $this->requestType().'_booking_request_created';
    }

    private function requestType(): string
    {
        return $this->bookingRequest instanceof Event ? 'event' : 'booth';
    }
}
