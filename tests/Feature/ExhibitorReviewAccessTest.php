<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('exhibitor can view reviewer details only for a review on accessible content', function (): void {
    $company = reviewAccessCompany();
    $companyMember = $this->createExhibitor();
    $outsider = $this->createExhibitor();
    $company->systemUsers()->attach($companyMember->id, ['created_at' => now()]);
    $event = reviewAccessEvent($company);
    $reviewer = $this->createVisitor();
    $review = Review::query()->create([
        'user_id' => $reviewer->id,
        'reviewable_type' => Event::class,
        'reviewable_id' => $event->id,
        'rating' => 5,
        'comment' => 'A verified review.',
    ]);

    $this->actingAs($outsider, 'system')
        ->getJson("/api/v1/exhibitor/reviews/reviewer/{$review->id}")
        ->assertForbidden();

    $this->actingAs($companyMember, 'system')
        ->getJson("/api/v1/exhibitor/reviews/reviewer/{$review->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $reviewer->id);
});

test('company members can view booth reviews while unrelated exhibitors cannot', function (): void {
    $company = reviewAccessCompany();
    $companyMember = $this->createExhibitor();
    $outsider = $this->createExhibitor();
    $company->systemUsers()->attach($companyMember->id, ['created_at' => now()]);
    $booth = new Booth(['company_id' => $company->id]);

    expect(Gate::forUser($companyMember)->allows('viewReviews', $booth))->toBeTrue()
        ->and(Gate::forUser($outsider)->allows('viewReviews', $booth))->toBeFalse();
});

test('company members can view event review statistics in one aggregated response', function (): void {
    $company = reviewAccessCompany();
    $companyMember = $this->createExhibitor();
    $company->systemUsers()->attach($companyMember->id, ['created_at' => now()]);
    $event = reviewAccessEvent($company);

    foreach ([1, 2, 3, 4, 5, 5] as $rating) {
        Review::query()->create([
            'user_id' => $this->createVisitor()->id,
            'reviewable_type' => Event::class,
            'reviewable_id' => $event->id,
            'rating' => $rating,
            'comment' => 'Statistics review.',
        ]);
    }

    $this->actingAs($companyMember, 'system')
        ->getJson("/api/v1/exhibitor/reviews/event/{$event->id}/statistics")
        ->assertOk()
        ->assertJsonPath('data.total_reviews', 6)
        ->assertJsonPath('data.average_rating', 3.3)
        ->assertJsonPath('data.one_star_reviews', 1)
        ->assertJsonPath('data.two_star_reviews', 1)
        ->assertJsonPath('data.three_star_reviews', 1)
        ->assertJsonPath('data.four_star_reviews', 1)
        ->assertJsonPath('data.five_star_reviews', 2);
});

function reviewAccessCompany(): Company
{
    return Company::query()->create([
        'name' => 'Review Access Company '.str()->random(4),
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for review access tests.',
        'status' => Status::APPROVED->value,
    ]);
}

function reviewAccessEvent(Company $company): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-REVIEW-'.str()->random(6),
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    /** @var Event $event */
    $event = $company->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Review Access Event',
        'description' => 'Event description for review access testing.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);

    return $event;
}

test('exhibitor event reviews are ordered from newest to oldest by default', function (): void {
    $company = reviewAccessCompany();
    $companyMember = $this->createExhibitor();
    $company->systemUsers()->attach($companyMember->id, ['created_at' => now()]);
    $event = reviewAccessEvent($company);

    $oldestReview = Review::query()->create([
        'user_id' => $this->createVisitor()->id,
        'reviewable_type' => Event::class,
        'reviewable_id' => $event->id,
        'rating' => 3,
        'comment' => 'An older review.',
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);
    $newestReview = Review::query()->create([
        'user_id' => $this->createVisitor()->id,
        'reviewable_type' => Event::class,
        'reviewable_id' => $event->id,
        'rating' => 5,
        'comment' => 'The newest review.',
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    $this->actingAs($companyMember, 'system')
        ->getJson("/api/v1/exhibitor/reviews/event/{$event->id}")
        ->assertOk()
        ->assertJsonPath('data.reviews.data.0.id', $newestReview->id)
        ->assertJsonPath('data.reviews.data.1.id', $oldestReview->id);
});
