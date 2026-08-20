<?php

use App\Channels\FcmChannel;
use App\Http\Resources\Shared\NotificationResource;
use App\Models\Report;
use App\Models\Review;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Admin\NewReportNotification;
use App\Notifications\SystemUser\Exhibitor\NewReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

it('builds report notifications for database and FCM only', function (): void {
    $report = new Report(['reportable_type' => 'App\\Models\\Event', 'reportable_id' => 4]);
    $report->forceFill(['id' => 11]);
    $notifiable = new SystemUser(['name' => 'Admin']);

    $notification = new NewReportNotification($report);

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe(['type' => 'report_created', 'title' => 'notifications.report_created_title', 'body' => 'notifications.report_created_body', 'target_id' => 11, 'reportable_type' => 'App\\Models\\Event', 'reportable_id' => 4]);
});

it('serializes numeric notification target IDs as integers for the API', function (): void {
    $notification = new DatabaseNotification([
        'id' => 'a1f9c67e-9ed8-4b7d-b513-b4982033b1c3',
        'data' => [
            'type' => 'review_created',
            'title' => 'notifications.review_created_title',
            'body' => 'notifications.review_created_body',
            'target_id' => '14',
            'reviewable_type' => 'App\\Models\\Booth',
            'reviewable_id' => 9,
            'rating' => 5,
        ],
    ]);

    $payload = (new NotificationResource($notification))->toArray(new Request);

    expect($payload['target_id'])->toBeInt()->toBe(14)
        ->and($payload['data'])->toBe([
            'reviewable_type' => 'App\\Models\\Booth',
            'reviewable_id' => 9,
            'rating' => 5,
        ]);
});

it('builds review notifications for database and FCM only', function (): void {
    $review = new Review(['reviewable_type' => 'App\\Models\\Booth', 'reviewable_id' => 9, 'rating' => 5]);
    $review->forceFill(['id' => 14]);
    $notifiable = new SystemUser(['name' => 'Exhibitor']);

    $notification = new NewReviewNotification($review);

    expect($notification->via($notifiable))->toBe(['database', FcmChannel::class])
        ->and($notification->toDatabase($notifiable))->toBe(['type' => 'review_created', 'title' => 'notifications.review_created_title', 'body' => 'notifications.review_created_body', 'target_id' => 14, 'reviewable_type' => 'App\\Models\\Booth', 'reviewable_id' => 9, 'rating' => 5])
        ->and($notification->toFcm($notifiable)['data'])->toBe(['type' => 'review_created', 'target_id' => '14', 'reviewable_type' => 'App\\Models\\Booth', 'reviewable_id' => '9', 'rating' => '5']);
});
