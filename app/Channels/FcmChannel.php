<?php

namespace App\Channels;

use App\Interfaces\FcmNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;

class FcmChannel
{
    private const MAX_MULTICAST_TARGETS = 500;

    public function __construct(
        protected Messaging $messaging,
    ) {}

    public function send($notifiable, Notification $notification): void
    {
        if (! $notification instanceof FcmNotification) {
            return;
        }

        $tokens = array_values(array_unique($notifiable->routeNotificationForFcm()));

        if (empty($tokens)) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        foreach (array_chunk($tokens, self::MAX_MULTICAST_TARGETS) as $tokenBatch) {
            try {
                $report = $this->messaging->sendMulticast($message, $tokenBatch);

                Log::info('FCM notification sent.', [
                    'success' => $report->successes()->count(),
                    'failed' => $report->failures()->count(),
                ]);

                foreach ($report->failures()->getItems() as $failure) {
                    Log::warning('FCM notification failure.', [
                        'token' => $failure->target()->value(),
                        'error' => $failure->error()->getMessage(),
                    ]);
                }

                $invalidTokens = array_merge(
                    $report->invalidTokens(),
                    $report->unknownTokens(),
                );

                if (! empty($invalidTokens)) {
                    $notifiable->deviceTokens()
                        ->whereIn('fcm_token', $invalidTokens)
                        ->delete();

                    Log::info('Invalid FCM tokens removed.', [
                        'count' => count($invalidTokens),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('FCM send failed.', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}
