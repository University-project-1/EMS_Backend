<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\Hall;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\CreatesActors;

uses(DatabaseMigrations::class, CreatesActors::class);

test('visitor can toggle an event in their saved items without affecting other records', function (): void {
    $this->seed(DatabaseSeeder::class);

    $visitor = $this->createVisitor();
    $event = Event::query()->firstOrFail();

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Event::class,
        'savedable_id' => $event->id,
    ]);

    $this->postJson("/api/v1/visitor/saved/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseMissing('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Event::class,
        'savedable_id' => $event->id,
    ]);
});

test('visitor cannot save an event until it is approved', function (): void {
    $visitor = $this->createVisitor();
    $event = createSaveEligibilityEvent(Status::PENDING);

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/events/{$event->id}")
        ->assertUnprocessable()
        ->assertJsonValidationErrors('event_id')
        ->assertJsonPath('errors.event_id.0', __('validation.invalid_status'));

    $this->assertDatabaseMissing('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Event::class,
        'savedable_id' => $event->id,
    ]);
});

test('visitor can save an approved event', function (): void {
    $visitor = $this->createVisitor();
    $event = createSaveEligibilityEvent(Status::APPROVED);

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Event::class,
        'savedable_id' => $event->id,
    ]);
});

test('visitor cannot save a booth until it is booked', function (): void {
    $visitor = $this->createVisitor();
    $booth = createSaveEligibilityBooth();

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/booths/{$booth->id}")
        ->assertUnprocessable()
        ->assertJsonValidationErrors('booth_id')
        ->assertJsonPath('errors.booth_id.0', __('validation.invalid_status'));

    $this->assertDatabaseMissing('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Booth::class,
        'savedable_id' => $booth->id,
    ]);
});

test('visitor can save a booked booth', function (): void {
    $visitor = $this->createVisitor();
    $booth = createSaveEligibilityBooth(createSaveEligibilityCompany()->id);

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/booths/{$booth->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Booth::class,
        'savedable_id' => $booth->id,
    ]);
});

test('visitor can retrieve booths they have saved', function (): void {
    $this->seed(DatabaseSeeder::class);

    $visitor = $this->createVisitor();
    $booth = Booth::query()->whereNotNull('company_id')->firstOrFail();

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/booths/{$booth->id}")
        ->assertOk();

    $this->getJson('/api/v1/visitor/saved/booths')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data');
});

function createSaveEligibilityEvent(Status $status): Event
{
    $company = createSaveEligibilityCompany();
    $eventHall = EventHall::query()->create([
        'number' => 'EVENT-HALL-'.str()->random(8),
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    return $company->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Save eligibility event',
        'description' => 'Event used to test the saved item eligibility rule.',
        'type' => EventType::OTHER->value,
        'status' => $status->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}

function createSaveEligibilityBooth(?int $companyId = null): Booth
{
    $hall = Hall::query()->create([
        'number' => 'BOOTH-HALL-'.str()->random(8),
        'area' => 1000,
        'type' => 'exhibition',
    ]);

    return Booth::query()->create([
        'hall_id' => $hall->id,
        'company_id' => $companyId,
        'number' => 'B-'.str()->random(8),
        'area' => 25,
        'price' => 500,
    ]);
}

function createSaveEligibilityCompany(): Company
{
    return Company::query()->create([
        'name' => 'Save Eligibility Company '.str()->random(8),
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used to test saved item eligibility.',
        'status' => Status::APPROVED->value,
    ]);
}
