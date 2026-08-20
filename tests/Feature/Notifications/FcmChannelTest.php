<?php

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MulticastSendReport;

it('splits mobile FCM multicast delivery into batches of at most 500 device tokens', function (): void {
    $tokens = array_map(
        fn (int $index): array => [
            'device_type' => 'android',
            'fcm_token' => "token-{$index}",
        ],
        range(1, 501),
    );
    $payload = [
        'notification' => ['title' => 'Title', 'body' => 'Body'],
        'data' => ['type' => 'test'],
    ];
    $report = MulticastSendReport::withItems([]);
    $batches = [];
    $messaging = Mockery::mock(Messaging::class);
    $messaging
        ->shouldReceive('sendMulticast')
        ->twice()
        ->with($payload, Mockery::type('array'))
        ->andReturnUsing(function (array $message, array $tokenBatch) use (&$batches, $report): MulticastSendReport {
            $batches[] = $tokenBatch;

            return $report;
        });

    $notifiable = new class($tokens)
    {
        /** @param list<array{device_type: string, fcm_token: string}> $tokens */
        public function __construct(private readonly array $tokens) {}

        public function deviceTokens(): object
        {
            return new class($this->tokens)
            {
                /** @param list<array{device_type: string, fcm_token: string}> $tokens */
                public function __construct(private readonly array $tokens) {}

                public function get(array $columns): Collection
                {
                    return collect($this->tokens)->map(
                        fn (array $token): object => (object) $token,
                    );
                }
            };
        }
    };
    $notification = new class extends Notification implements FcmNotification
    {
        public function toFcm(object $notifiable): array
        {
            return [
                'notification' => ['title' => 'Title', 'body' => 'Body'],
                'data' => ['type' => 'test'],
            ];
        }
    };

    (new FcmChannel($messaging))->send($notifiable, $notification);

    expect($batches)->toHaveCount(2)
        ->and($batches[0])->toHaveCount(500)
        ->and($batches[1])->toHaveCount(1);
});

it('sends data-only FCM messages to web devices while preserving notification messages for mobile devices', function (): void {
    $payload = [
        'notification' => ['title' => 'Title', 'body' => 'Body'],
        'data' => ['type' => 'test'],
    ];
    $report = MulticastSendReport::withItems([]);
    $messaging = Mockery::mock(Messaging::class);
    $messaging
        ->shouldReceive('sendMulticast')
        ->once()
        ->ordered()
        ->with($payload, ['mobile-token'])
        ->andReturn($report);
    $messaging
        ->shouldReceive('sendMulticast')
        ->once()
        ->ordered()
        ->with([
            'data' => [
                'type' => 'test',
                'web_notification_body' => 'Body',
                'web_notification_title' => 'Title',
            ],
        ], ['web-token'])
        ->andReturn($report);

    $notifiable = new class
    {
        public function deviceTokens(): object
        {
            return new class
            {
                public function get(array $columns): Collection
                {
                    return collect([
                        (object) ['device_type' => 'android', 'fcm_token' => 'mobile-token'],
                        (object) ['device_type' => 'web', 'fcm_token' => 'web-token'],
                    ]);
                }
            };
        }
    };
    $notification = new class extends Notification implements FcmNotification
    {
        public function toFcm(object $notifiable): array
        {
            return [
                'notification' => ['title' => 'Title', 'body' => 'Body'],
                'data' => ['type' => 'test'],
            ];
        }
    };

    (new FcmChannel($messaging))->send($notifiable, $notification);
});
