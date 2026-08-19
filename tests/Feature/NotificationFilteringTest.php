<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('visitor can filter all matching notifications by their payload type', function (): void {
    $visitor = $this->createVisitor();

    createVisitorNotification($visitor, 'review_created');
    createVisitorNotification($visitor, 'review_created');
    createVisitorNotification($visitor, 'review_created', now());
    createVisitorNotification($visitor, 'event_reminder');

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/notifications?filter[type]=review_created&per_page=10')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(3, 'data.data')
        ->assertJsonPath('data.data.0.type', 'review_created')
        ->assertJsonPath('data.data.1.type', 'review_created')
        ->assertJsonPath('data.data.2.type', 'review_created');
});

test('visitor can filter all unread matching notifications by their payload type', function (): void {
    $visitor = $this->createVisitor();

    createVisitorNotification($visitor, 'review_created');
    createVisitorNotification($visitor, 'review_created');
    createVisitorNotification($visitor, 'review_created', now());
    createVisitorNotification($visitor, 'event_reminder');

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/notifications/unread?filter[type]=review_created&per_page=10')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(2, 'data.data')
        ->assertJsonPath('data.data.0.type', 'review_created')
        ->assertJsonPath('data.data.1.type', 'review_created');
});

function createVisitorNotification(object $visitor, string $type, $readAt = null): void
{
    $visitor->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => 'App\\Notifications\\Mobile\\LeadInterestNotification',
        'data' => [
            'type' => $type,
            'title' => 'notifications.'.$type.'_title',
            'body' => 'notifications.'.$type.'_body',
            'target_id' => 1,
        ],
        'read_at' => $readAt,
    ]);
}
