<?php

namespace App\Channels;

use App\Enum\DeviceType;
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

    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notification instanceof FcmNotification) {
            return;
        }

        $deviceTokens = $notifiable->deviceTokens()
            ->get(['device_type', 'fcm_token']);

        if ($deviceTokens->isEmpty()) {
            return;
        }

        $message = $notification->toFcm($notifiable);
        $webTokens = $this->uniqueTokens(
            $deviceTokens
                ->where('device_type', DeviceType::WEB->value)
                ->pluck('fcm_token')
                ->all(),
        );
        $mobileTokens = $this->uniqueTokens(
            $deviceTokens
                ->where('device_type', '!=', DeviceType::WEB->value)
                ->pluck('fcm_token')
                ->all(),
        );

        $this->sendToTokens($notifiable, $message, $mobileTokens, 'mobile');
        $this->sendToTokens(
            $notifiable,
            $this->dataOnlyMessage($message),
            $webTokens,
            DeviceType::WEB->value,
        );
    }

    /**
     * @param  array<string, mixed>  $message
     * @param  list<string>  $tokens
     */
    private function sendToTokens(
        object $notifiable,
        array $message,
        array $tokens,
        string $deviceType,
    ): void {
        foreach (array_chunk($tokens, self::MAX_MULTICAST_TARGETS) as $tokenBatch) {
            try {
                $report = $this->messaging->sendMulticast($message, $tokenBatch);

                Log::info('FCM notification sent.', [
                    'device_type' => $deviceType,
                    'failed' => $report->failures()->count(),
                    'success' => $report->successes()->count(),
                ]);

                foreach ($report->failures()->getItems() as $failure) {
                    Log::warning('FCM notification failure.', [
                        'error' => $failure->error()->getMessage(),
                        'token' => $failure->target()->value(),
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
                    'device_type' => $deviceType,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $message
     * @return array<string, mixed>
     */
    private function dataOnlyMessage(array $message): array
    {
        $notification = $message['notification'] ?? [];
        $message['data'] = [
            ...($message['data'] ?? []),
            'web_notification_body' => (string) ($notification['body'] ?? ''),
            'web_notification_title' => (string) ($notification['title'] ?? ''),
        ];

        unset($message['notification']);

        return $message;
    }

    /**
     * @param  list<string>  $tokens
     * @return list<string>
     */
    private function uniqueTokens(array $tokens): array
    {
        return array_values(array_unique($tokens));
    }
}
