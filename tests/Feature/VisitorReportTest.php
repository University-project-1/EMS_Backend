<?php

use App\Enum\BusinessSectors;
use App\Enum\EventType;
use App\Enum\ReportStatus;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\User;
use App\Notifications\SystemUser\Admin\NewReportNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('visitor can report an approved event', function (): void {
    Notification::fake();
    $administrator = $this->createAdministrator();
    $visitor = $this->createVisitor();
    $event = createReportableVisitorEvent();

    $this->actingAs($visitor, 'mobile')
        ->postJson('/api/v1/visitor/report', [
            'event_id' => $event->id,
            'title' => 'Incorrect schedule',
            'description' => 'The published schedule does not match the event details.',
        ])
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('reports', [
        'reporter_type' => User::class,
        'reporter_id' => $visitor->id,
        'reportable_type' => Event::class,
        'reportable_id' => $event->id,
        'title' => 'Incorrect schedule',
        'status' => ReportStatus::PENDING->value,
    ]);

    Notification::assertSentTo($administrator, NewReportNotification::class);
});

test('visitor cannot submit a report without a title', function (): void {
    $visitor = $this->createVisitor();
    $event = createReportableVisitorEvent();

    $this->actingAs($visitor, 'mobile')
        ->postJson('/api/v1/visitor/report', [
            'event_id' => $event->id,
            'description' => 'A title is required to submit this report.',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

function createReportableVisitorEvent(): Event
{
    $eventHall = EventHall::query()->create([
        'number' => 'HALL-501-'.str()->random(4),
        'area' => 120,
        'price_per_hour' => 250,
    ]);
    $company = Company::query()->create([
        'name' => 'Report Event Company '.str()->random(4),
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for a visitor report test.',
        'status' => Status::APPROVED->value,
    ]);
    $startAt = now()->addDays(3)->startOfHour();

    return $company->events()->create([
        'event_hall_id' => $eventHall->id,
        'title' => 'Reportable event',
        'description' => 'Event description for testing.',
        'type' => EventType::OTHER->value,
        'status' => Status::APPROVED->value,
        'start_at' => $startAt,
        'end_at' => $startAt->copy()->addHour(),
        'duration' => 60,
    ]);
}
