<?php

use App\Channels\FcmChannel;
use App\Interfaces\FcmNotification;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MulticastSendReport;

it('splits FCM multicast delivery into batches of at most 500 device tokens', function (): void {
    $tokens = array_map(
        fn (int $index): string => "token-{$index}",
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
        /** @param list<string> $tokens */
        public function __construct(private readonly array $tokens) {}

        /** @return list<string> */
        public function routeNotificationForFcm(): array
        {
            return $this->tokens;
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
