<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\Hall;
use App\Models\SystemUser;
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

test('visitor review notifies a direct system user event owner', function (): void {
    Notification::fake();
    $organizer = $this->createExhibitor();
    $visitor = $this->createVisitor();
    $event = createOrganizerReviewEvent($organizer);

    $this->actingAs($visitor, 'mobile')
        ->postJson('/api/v1/visitor/reviews', [
            'event_id' => $event->id,
            'rating' => 5,
            'comment' => 'Very useful event.',
        ])
        ->assertOk();

    Notification::assertSentTo($organizer, NewReviewNotification::class);
});

test('event reviews return the current visitor review before other reviews without duplication', function (): void {
    $visitor = $this->createVisitor();
    $otherVisitor = $this->createVisitor();
    $secondOtherVisitor = $this->createVisitor();
    $event = createReviewableVisitorEvent();

    $currentUserReview = $event->reviews()->create([
        'user_id' => $visitor->id,
        'rating' => 5,
        'comment' => 'My event review.',
    ]);
    $event->reviews()->create([
        'user_id' => $otherVisitor->id,
        'rating' => 4,
        'comment' => 'Another event review.',
    ]);
    $latestOtherReview = $event->reviews()->create([
        'user_id' => $secondOtherVisitor->id,
        'rating' => 3,
        'comment' => 'Latest event review.',
    ]);

    $response = $this->actingAs($visitor, 'mobile')
        ->getJson("/api/v1/visitor/reviews/event/{$event->id}?per_page=1")
        ->assertOk()
        ->assertJsonPath('data.current_user_review.id', $currentUserReview->id)
        ->assertJsonPath('data.current_user_review.comment', 'My event review.')
        ->assertJsonPath('data.reviews.per_page', 1)
        ->assertJsonCount(1, 'data.reviews.data');

    expect(collect($response->json('data.reviews.data'))->pluck('id')->all())
        ->toBe([$latestOtherReview->id]);
});

test('booth reviews return the current visitor review before other reviews without duplication', function (): void {
    $visitor = $this->createVisitor();
    $otherVisitor = $this->createVisitor();
    $secondOtherVisitor = $this->createVisitor();
    $booth = createReviewableVisitorBooth();

    $currentUserReview = $booth->reviews()->create([
        'user_id' => $visitor->id,
        'rating' => 5,
        'comment' => 'My booth review.',
    ]);
    $booth->reviews()->create([
        'user_id' => $otherVisitor->id,
        'rating' => 4,
        'comment' => 'Another booth review.',
    ]);
    $latestOtherReview = $booth->reviews()->create([
        'user_id' => $secondOtherVisitor->id,
        'rating' => 3,
        'comment' => 'Latest booth review.',
    ]);

    $response = $this->actingAs($visitor, 'mobile')
        ->getJson("/api/v1/visitor/reviews/booth/{$booth->id}?per_page=1")
        ->assertOk()
        ->assertJsonPath('data.current_user_review.id', $currentUserReview->id)
        ->assertJsonPath('data.current_user_review.comment', 'My booth review.')
        ->assertJsonPath('data.reviews.per_page', 1)
        ->assertJsonCount(1, 'data.reviews.data');

    expect(collect($response->json('data.reviews.data'))->pluck('id')->all())
        ->toBe([$latestOtherReview->id]);
});

test('visitor review listings ignore exhibitor filters and sorts', function (): void {
    $firstVisitor = $this->createVisitor();
    $secondVisitor = $this->createVisitor();
    $readingVisitor = $this->createVisitor();
    $event = createReviewableVisitorEvent();

    $event->reviews()->create([
        'user_id' => $firstVisitor->id,
        'rating' => 5,
        'comment' => 'Five-star review.',
    ]);
    $event->reviews()->create([
        'user_id' => $secondVisitor->id,
        'rating' => 3,
        'comment' => 'Three-star review.',
    ]);

    $response = $this->actingAs($readingVisitor, 'mobile')->getJson(
        "/api/v1/visitor/reviews/event/{$event->id}?per_page=1&filter[rating]=5&sort=rating"
    )
        ->assertOk()
        ->assertJsonPath('data.reviews.per_page', 1)
        ->assertJsonPath('data.reviews.data.0.rating', 3)
        ->assertJsonCount(1, 'data.reviews.data');

    $nextCursor = $response->json('data.reviews.next_cursor');

    expect($nextCursor)->not->toBeNull();

    $this->actingAs($readingVisitor, 'mobile')
        ->getJson("/api/v1/visitor/reviews/event/{$event->id}?per_page=1&cursor={$nextCursor}")
        ->assertOk()
        ->assertJsonPath('data.reviews.per_page', 1)
        ->assertJsonPath('data.reviews.data.0.rating', 5);
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

function createReviewableVisitorBooth(): Booth
{
    $hall = Hall::query()->create([
        'number' => 'HALL-BOOTH-'.str()->random(4),
        'area' => 120,
        'type' => 'exhibition',
    ]);
    $company = Company::query()->create([
        'name' => 'Review Booth Company '.str()->random(4),
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for a visitor review test.',
        'status' => Status::APPROVED->value,
    ]);

    return $hall->booths()->create([
        'company_id' => $company->id,
        'number' => 'BOOTH-'.str()->random(4),
        'area' => 25,
        'price' => 500,
    ]);
}

function createOrganizerReviewEvent(SystemUser $organizer): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-ORGANIZER-'.str()->random(4),
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    return $organizer->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Organizer reviewable event',
        'description' => 'A direct organizer event used for a visitor review test.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}

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
