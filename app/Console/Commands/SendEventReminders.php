<?php

namespace App\Console\Commands;

use App\Enum\Status;
use App\Models\Event;
use App\Models\EventReminder;
use App\Models\Saved;
use App\Models\User;
use App\Notifications\Mobile\EventReminderNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notifications:send-event-reminders')]
#[Description('Send reminders for saved events starting in fifteen minutes.')]
class SendEventReminders extends Command
{
    public function handle(): int
    {
        $windowStart = now()->addMinutes(15);
        $windowEnd = (clone $windowStart)->addMinute();
        $sentReminders = 0;

        Event::query()
            ->where('status', Status::APPROVED->value)
            ->whereBetween('start_at', [$windowStart, $windowEnd])
            ->with('savedItems.user')
            ->chunkById(100, function ($events) use (&$sentReminders): void {
                foreach ($events as $event) {
                    foreach ($event->savedItems as $saved) {
                        if (! $saved instanceof Saved || ! $saved->user instanceof User) {
                            continue;
                        }

                        $reminder = EventReminder::query()->firstOrCreate(
                            [
                                'event_id' => $event->getKey(),
                                'user_id' => $saved->user->getKey(),
                            ],
                            ['reminded_at' => now()],
                        );

                        if (! $reminder->wasRecentlyCreated) {
                            continue;
                        }

                        $saved->user->notify(new EventReminderNotification($event));
                        $sentReminders++;
                    }
                }
            });

        $this->info("Sent {$sentReminders} event reminder(s).");

        return self::SUCCESS;
    }
}
