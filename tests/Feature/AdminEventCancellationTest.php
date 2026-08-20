<?php

use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Exhibitor\EventCancellationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('admin can cancel an approved event and notify its owner', function (): void {
    Notification::fake();

    $admin = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $eventHall = eventCancellationHall('EVENT-HALL-CANCEL-701');
    $event = eventCancellationEvent($exhibitor->getKey(), $eventHall->getKey());

    $this->actingAs($admin, 'system')
        ->patchJson("/api/v1/admin/events/requests/{$event->id}/cancel")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'status' => Status::CANCELED->value,
        'qr_token' => null,
    ]);
    Notification::assertSentTo(
        $exhibitor,
        EventCancellationNotification::class,
        fn (EventCancellationNotification $notification): bool => $notification->event->is($event),
    );
});

test('admin cannot cancel a non-approved event', function (): void {
    Notification::fake();

    $admin = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $eventHall = eventCancellationHall('EVENT-HALL-CANCEL-702');
    $event = eventCancellationEvent($exhibitor->getKey(), $eventHall->getKey(), Status::PENDING);

    $this->actingAs($admin, 'system')
        ->patchJson("/api/v1/admin/events/requests/{$event->id}/cancel")
        ->assertStatus(400);

    Notification::assertNothingSent();
});

function eventCancellationHall(string $number): EventHall
{
    return EventHall::query()->create([
        'number' => $number,
        'area' => 1000,
        'price_per_hour' => 500,
    ]);
}

function eventCancellationEvent(int $ownerId, int $eventHallId, Status $status = Status::APPROVED): Event
{
    return Event::query()->create([
        'eventable_type' => SystemUser::class,
        'eventable_id' => $ownerId,
        'event_hall_id' => $eventHallId,
        'type' => EventType::CONFERENCE,
        'status' => $status,
        'qr_token' => 'E-'.$ownerId.'-cancel-token',
        'title' => 'Event Cancellation Test',
        'description' => 'An event used for cancellation tests.',
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(2),
        'duration' => 24,
    ]);
}
