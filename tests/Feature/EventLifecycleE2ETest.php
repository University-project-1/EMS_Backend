<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\ReportStatus;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('event lifecycle connects exhibitor visibility visitor feedback and administrator report resolution', function (): void {
    Notification::fake();

    $administrator = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $visitor = $this->createVisitor();
    $company = createLifecycleCompany();
    $company->systemUsers()->attach($exhibitor->id, [
        'created_at' => now(),
    ]);
    $event = createLifecycleEvent($company);

    $this->actingAs($exhibitor, 'system')
        ->getJson('/api/v1/exhibitor/events')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.data.0.id', $event->id);

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/events')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.data.0.id', $event->id);

    $this->postJson('/api/v1/visitor/reviews', [
        'event_id' => $event->id,
        'rating' => 5,
        'comment' => 'A helpful and well-organized event.',
    ])->assertOk()->assertJsonPath('status', true);

    $this->postJson('/api/v1/visitor/report', [
        'event_id' => $event->id,
        'title' => 'Schedule requires clarification',
        'description' => 'The published schedule needs an updated start time.',
    ])->assertOk()->assertJsonPath('status', true);

    $report = Report::query()->where('reporter_id', $visitor->id)->sole();

    $this->actingAs($administrator, 'system')
        ->postJson("/api/v1/admin/reports/{$report->id}/resolved", [
            'notes' => 'The schedule was verified and clarified.',
        ])
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('reviews', [
        'user_id' => $visitor->id,
        'reviewable_type' => Event::class,
        'reviewable_id' => $event->id,
        'rating' => 5,
    ]);
    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::RESOLVED->value,
        'resolved_by' => $administrator->id,
        'admin_notes' => 'The schedule was verified and clarified.',
    ]);
});

function createLifecycleCompany(): Company
{
    return Company::query()->create([
        'name' => 'Lifecycle Company',
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for the event lifecycle journey test.',
        'status' => Status::APPROVED->value,
    ]);
}

function createLifecycleEvent(Company $company): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-701',
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    return $company->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Lifecycle event',
        'description' => 'Event description for journey testing.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}
