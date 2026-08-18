<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Enum\ReportStatus;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\Event;
use App\Models\EventReminder;
use App\Models\Lead;
use App\Models\Report;
use App\Models\Review;
use App\Models\Saved;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

final class RealDataUserInteractionsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()
            ->where('email', 'like', '%@approved-users.test')
            ->get()
            ->shuffle()
            ->values();

        $booths = Booth::query()
            ->whereNotNull('company_id')
            ->get()
            ->shuffle()
            ->values();

        $events = Event::query()
            ->where('status', Status::APPROVED)
            ->get()
            ->shuffle()
            ->values();

        if ($users->isEmpty() || ($booths->isEmpty() && $events->isEmpty())) {
            $this->command?->warn('RealDataUserInteractionsSeeder skipped: required users or targets are missing.');
            return;
        }

        $targets = $booths->concat($events)->values();
        $base = Carbon::create(2026, 8, 17, 9, 0, 0);
        $comments = [
            'Useful information and a well-presented exhibition presence.',
            'The details were clear and relevant to my visit.',
            'Good experience; I would like to follow up after the exhibition.',
            'Interesting topic and helpful information for visitors.',
            'The presentation was practical and easy to understand.',
        ];

        // Every action uses stable unique keys, so rerunning the seeder is safe.
        for ($i = 0; $i < 180; $i++) {
            $user = $users[$i % $users->count()];
            $target = $targets[$i % $targets->count()];
            $at = $base->copy()->addDays($i % 7)->setTime(9 + ($i % 8), ($i * 7) % 60);

            Review::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'reviewable_type' => $target::class,
                    'reviewable_id' => $target->id,
                ],
                [
                    'rating' => 3 + ($i % 3),
                    'comment' => $comments[$i % count($comments)],
                    'created_at' => $at,
                    'updated_at' => $at,
                ],
            );

            Lead::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'leadable_type' => $target::class,
                    'leadable_id' => $target->id,
                ],
                ['created_at' => $at],
            );

            Saved::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'savedable_type' => $target::class,
                    'savedable_id' => $target->id,
                ],
                ['created_at' => $at],
            );
        }

        for ($i = 0; $i < 70; $i++) {
            $user = $users[($i * 3 + 5) % $users->count()];
            $target = $targets[($i * 5 + 2) % $targets->count()];
            $at = $base->copy()->addDays(($i + 1) % 7)->setTime(10 + ($i % 6), 0);

            Report::query()->firstOrCreate(
                [
                    'reporter_type' => User::class,
                    'reporter_id' => $user->id,
                    'reportable_type' => $target::class,
                    'reportable_id' => $target->id,
                    'title' => 'Visitor feedback: '.$target->getKey(),
                ],
                [
                    'description' => 'Visitor reported that this information should be reviewed or clarified for a better exhibition experience.',
                    'status' => ReportStatus::PENDING,
                    'resolved_by' => null,
                    'created_at' => $at,
                    'updated_at' => $at,
                ],
            );
        }

        for ($i = 0; $i < min(120, $events->count() * $users->count()); $i++) {
            $event = $events[$i % $events->count()];
            $user = $users[($i * 7 + 1) % $users->count()];
            $at = $base->copy()->addDays($i % 7)->setTime(8 + ($i % 10), 0);

            EventReminder::query()->updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $user->id],
                ['reminded_at' => $at],
            );
        }

        $this->command?->info('Seeded one week of realistic ordinary-user interactions: reviews, leads, saved items, reports, and event reminders.');
    }
}
