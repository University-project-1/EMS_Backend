<?php

use App\Enum\Status;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\EventReminder;
use App\Models\Saved;
use App\Models\User;
use App\Notifications\Mobile\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function createReminderEvent(Status $status = Status::APPROVED): Event
{
    $organizer = User::factory()->create();
    $eventHall = EventHall::query()->create([
        'number' => 'EH-'.fake()->unique()->numberBetween(1000, 9999),
        'area' => 100,
        'price_per_hour' => 500,
    ]);

    return Event::query()->create([
        'eventable_type' => User::class,
        'eventable_id' => $organizer->getKey(),
        'event_hall_id' => $eventHall->getKey(),
        'title' => 'Reminder Test Event',
        'description' => 'An event used to verify scheduled reminders.',
        'status' => $status,
        'start_at' => now()->addMinutes(15)->addSeconds(30),
        'duration' => 60,
        'end_at' => now()->addMinutes(75)->addSeconds(30),
        'price' => 0,
    ]);
}

it('sends one reminder to each user who saved an approved event', function (): void {
    Carbon::setTestNow(Carbon::parse('2026-08-14 10:00:00'));
    Notification::fake();

    $event = createReminderEvent();
    $savedBy = User::factory()->create();
    $notSavedBy = User::factory()->create();
    Saved::query()->create([
        'user_id' => $savedBy->getKey(),
        'savedable_type' => Event::class,
        'savedable_id' => $event->getKey(),
    ]);

    $this->artisan('notifications:send-event-reminders')->assertSuccessful();

    Notification::assertSentTo($savedBy, EventReminderNotification::class);
    Notification::assertNotSentTo($notSavedBy, EventReminderNotification::class);
    expect(EventReminder::query()->where('event_id', $event->getKey())->count())->toBe(1);

    $this->artisan('notifications:send-event-reminders')->assertSuccessful();

    Notification::assertSentToTimes($savedBy, EventReminderNotification::class, 1);
    expect(EventReminder::query()->where('event_id', $event->getKey())->count())->toBe(1);
});

it('does not remind users about unapproved events', function (): void {
    Carbon::setTestNow(Carbon::parse('2026-08-14 10:00:00'));
    Notification::fake();

    $event = createReminderEvent(Status::PENDING);
    $savedBy = User::factory()->create();
    Saved::query()->create([
        'user_id' => $savedBy->getKey(),
        'savedable_type' => Event::class,
        'savedable_id' => $event->getKey(),
    ]);

    $this->artisan('notifications:send-event-reminders')->assertSuccessful();

    Notification::assertNothingSent();
    expect(EventReminder::query()->count())->toBe(0);
});

afterEach(function (): void {
    Carbon::setTestNow();
});
