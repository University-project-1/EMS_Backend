<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Enum\ReportStatus;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
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

        $acceptedBoothIds = BoothRequest::query()
            ->where('status', Status::APPROVED)
            ->whereNotNull('booth_id')
            ->pluck('booth_id')
            ->unique()
            ->values();

        $booths = Booth::query()
            ->whereIn('id', $acceptedBoothIds)
            ->whereNotNull('company_id')
            ->get()
            ->shuffle()
            ->values();

        $events = Event::query()
            ->where('status', Status::APPROVED)
            ->get()
            ->shuffle()
            ->values();

        $approvedEventIds = $events->pluck('id')->values();
        $acceptedBoothIdValues = $acceptedBoothIds->all();
        $approvedEventIdValues = $approvedEventIds->all();

        // Remove legacy actions that point to targets which are not accepted/approved.
        Review::query()->where('reviewable_type', Booth::class)->whereNotIn('reviewable_id', $acceptedBoothIdValues)->delete();
        // Rebuild accepted-booth reviews from the deterministic 15–32 range so
        // older review seeders cannot push a booth above the requested maximum.
        Review::query()->where('reviewable_type', Booth::class)->whereIn('reviewable_id', $acceptedBoothIdValues)->delete();
        Review::query()->where('reviewable_type', Event::class)->whereNotIn('reviewable_id', $approvedEventIdValues)->delete();
        Lead::query()->where('leadable_type', Booth::class)->whereNotIn('leadable_id', $acceptedBoothIdValues)->delete();
        Lead::query()->where('leadable_type', Event::class)->whereNotIn('leadable_id', $approvedEventIdValues)->delete();
        Saved::query()->where('savedable_type', Booth::class)->whereNotIn('savedable_id', $acceptedBoothIdValues)->delete();
        Saved::query()->where('savedable_type', Event::class)->whereNotIn('savedable_id', $approvedEventIdValues)->delete();
        Report::query()->where('reportable_type', Booth::class)->whereNotIn('reportable_id', $acceptedBoothIdValues)->delete();
        Report::query()->where('reportable_type', Event::class)->whereNotIn('reportable_id', $approvedEventIdValues)->delete();
        EventReminder::query()->whereNotIn('event_id', $approvedEventIdValues)->delete();

        if ($users->isEmpty() || ($booths->isEmpty() && $events->isEmpty())) {
            $this->command?->warn('RealDataUserInteractionsSeeder skipped: required users or accepted targets are missing.');
            return;
        }

        // Include every accepted booth and every approved event in the coverage pool.
        $popularBooths = $booths->values();
        $popularEvents = $events->values();
        $targets = $popularBooths->concat($popularEvents)->values();
        $base = Carbon::create(2026, 8, 17, 9, 0, 0);
        $comments = [
            'Useful information and a well-presented exhibition presence.',
            'The details were clear and relevant to my visit.',
            'Good experience; I would like to follow up after the exhibition.',
            'Interesting topic and helpful information for visitors.',
            'The presentation was practical and easy to understand.',
        ];

        // Target-first distribution with different engagement profiles:
        // events are review-heavy; leads always exceed saves; booth reviews stay
        // below booth saves while every valid target remains covered.
        foreach ($popularEvents as $targetIndex => $event) {
            $eventReviewCount = min(20 + (($targetIndex * 7) % 21), $users->count());
            $eventSavedCount = min(8 + (($targetIndex * 5) % 11), $users->count());
            $eventLeadCount = min(max($eventSavedCount + 4, 12 + (($targetIndex * 3) % 17)), $users->count());
            for ($j = 0; $j < $eventReviewCount; $j++) {
                $user = $users[(($targetIndex * 20) + $j) % $users->count()];
                $at = $base->copy()->addDays(($targetIndex + $j) % 7)->setTime(9 + (($targetIndex + $j) % 8), ($j * 7) % 60);
                Review::query()->updateOrCreate(
                    ['user_id' => $user->id, 'reviewable_type' => Event::class, 'reviewable_id' => $event->id],
                    ['rating' => 3 + (($targetIndex + $j) % 3), 'comment' => $comments[($targetIndex + $j) % count($comments)], 'created_at' => $at, 'updated_at' => $at],
                );
            }
            for ($j = 0; $j < $eventLeadCount; $j++) {
                $user = $users[(($targetIndex * 12) + $j + 7) % $users->count()];
                $at = $base->copy()->addDays(($targetIndex + $j + 1) % 7)->setTime(10 + ($j % 7), 0);
                Lead::query()->firstOrCreate(['user_id' => $user->id, 'leadable_type' => Event::class, 'leadable_id' => $event->id], ['created_at' => $at]);
            }
            for ($j = 0; $j < $eventSavedCount; $j++) {
                $user = $users[(($targetIndex * 8) + $j + 13) % $users->count()];
                $at = $base->copy()->addDays(($targetIndex + $j + 2) % 7)->setTime(11 + ($j % 6), 0);
                Saved::query()->firstOrCreate(['user_id' => $user->id, 'savedable_type' => Event::class, 'savedable_id' => $event->id], ['created_at' => $at]);
            }
        }

        foreach ($popularBooths as $targetIndex => $booth) {
            // Doubled booth engagement ranges: reviews remain below saves,
            // while leads remain above saves for every accepted booth.
            $boothReviewCount = min(15 + (($targetIndex * 5) % 18), $users->count());
            $boothSavedCount = min(20 + (($targetIndex * 5) % 22), $users->count());
            $boothLeadCount = min(max($boothSavedCount + 8, 28 + (($targetIndex * 2) % 30)), $users->count());
            for ($j = 0; $j < $boothReviewCount; $j++) {
                $user = $users[(($targetIndex * 4) + $j + 3) % $users->count()];
                $at = $base->copy()->addDays(($targetIndex + $j) % 7)->setTime(9 + ($j % 8), ($j * 11) % 60);
                Review::query()->updateOrCreate(
                    ['user_id' => $user->id, 'reviewable_type' => Booth::class, 'reviewable_id' => $booth->id],
                    ['rating' => 3 + (($targetIndex + $j) % 3), 'comment' => $comments[($targetIndex + $j) % count($comments)], 'created_at' => $at, 'updated_at' => $at],
                );
            }
            for ($j = 0; $j < $boothLeadCount; $j++) {
                $user = $users[(($targetIndex * 10) + $j + 17) % $users->count()];
                $at = $base->copy()->addDays(($targetIndex + $j + 1) % 7)->setTime(10 + ($j % 7), 0);
                Lead::query()->firstOrCreate(['user_id' => $user->id, 'leadable_type' => Booth::class, 'leadable_id' => $booth->id], ['created_at' => $at]);
            }
            for ($j = 0; $j < $boothSavedCount; $j++) {
                $user = $users[(($targetIndex * 8) + $j + 23) % $users->count()];
                $at = $base->copy()->addDays(($targetIndex + $j + 2) % 7)->setTime(11 + ($j % 6), 0);
                Saved::query()->firstOrCreate(['user_id' => $user->id, 'savedable_type' => Booth::class, 'savedable_id' => $booth->id], ['created_at' => $at]);
            }
        }

        for ($i = 0; $i < 90; $i++) {
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

        if ($popularEvents->isNotEmpty()) {
            for ($i = 0; $i < min(180, $popularEvents->count() * $users->count()); $i++) {
                $event = $popularEvents[$i % $popularEvents->count()];
                $user = $users[($i * 7 + 1) % $users->count()];
                $at = $base->copy()->addDays($i % 7)->setTime(8 + ($i % 10), 0);

                EventReminder::query()->updateOrCreate(
                    ['event_id' => $event->id, 'user_id' => $user->id],
                    ['reminded_at' => $at],
                );
            }
        }

        $this->command?->info('Seeded expanded weekly interactions for all ordinary users on accepted booths and approved events.');
    }
}
