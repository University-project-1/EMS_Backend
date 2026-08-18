<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Notifications\SystemUser\Exhibitor\NewReviewNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('visitor can review an approved event', function (): void {
    Notification::fake();
    $exhibitor = $this->createExhibitor();
    $visitor = $this->createVisitor();
    $event = createReviewableVisitorEvent();
    $event->eventable->systemUsers()->attach($exhibitor->id, [
        'created_at' => now(),
    ]);

    $this->actingAs($visitor, 'mobile')
        ->postJson('/api/v1/visitor/reviews', [
            'event_id' => $event->id,
            'rating' => 5,
            'comment' => 'Very useful event.',
        ])
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('reviews', [
        'user_id' => $visitor->id,
        'reviewable_type' => Event::class,
        'reviewable_id' => $event->id,
        'rating' => 5,
        'comment' => 'Very useful event.',
    ]);

    Notification::assertSentTo($exhibitor, NewReviewNotification::class);
});

test('visitor cannot submit a review with an invalid rating', function (): void {
    $visitor = $this->createVisitor();
    $event = createReviewableVisitorEvent();

    $this->actingAs($visitor, 'mobile')
        ->postJson('/api/v1/visitor/reviews', [
            'event_id' => $event->id,
            'rating' => 6,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('rating');
});

function createReviewableVisitorEvent(): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-401-'.str()->random(4),
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $company = Company::query()->create([
        'name' => 'Review Event Company '.str()->random(4),
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for a visitor review test.',
        'status' => Status::APPROVED->value,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    return $company->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Reviewable event',
        'description' => 'Event description for testing.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}
