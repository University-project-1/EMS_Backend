<?php

use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Exhibitor\EventPaymentReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('admin can send a queued payment reminder for a pending event', function (): void {
    Notification::fake();

    $admin = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $eventHall = EventHall::query()->create([
        'number' => 'EVENT-HALL-PAYMENT-701',
        'area' => 1000,
        'price_per_hour' => 500,
    ]);
    $event = paymentReminderEvent($exhibitor->getKey(), $eventHall->getKey(), Status::PENDING);

    $this->actingAs($admin, 'system')
        ->postJson("/api/v1/admin/events/requests/{$event->id}/payment-reminder")
        ->assertOk()
        ->assertJsonPath('status', true);

    Notification::assertSentTo(
        $exhibitor,
        EventPaymentReminderNotification::class,
        fn (EventPaymentReminderNotification $notification): bool => $notification->event->is($event),
    );
});

test('admin cannot send a payment reminder for an approved event', function (): void {
    Notification::fake();

    $admin = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $eventHall = EventHall::query()->create([
        'number' => 'EVENT-HALL-PAYMENT-702',
        'area' => 1000,
        'price_per_hour' => 500,
    ]);
    $event = paymentReminderEvent($exhibitor->getKey(), $eventHall->getKey(), Status::APPROVED);

    $this->actingAs($admin, 'system')
        ->postJson("/api/v1/admin/events/requests/{$event->id}/payment-reminder")
        ->assertStatus(400);

    Notification::assertNothingSent();
});

function paymentReminderEvent(int $ownerId, int $eventHallId, Status $status = Status::APPROVED): Event
{
    return Event::query()->create([
        'eventable_type' => SystemUser::class,
        'eventable_id' => $ownerId,
        'event_hall_id' => $eventHallId,
        'type' => EventType::CONFERENCE,
        'status' => $status,
        'title' => 'Payment Reminder Event',
        'description' => 'An event used for payment reminder tests.',
        'start_at' => now()->addDay(),
        'end_at' => now()->addDays(2),
        'duration' => 24,
    ]);
}
