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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class RealDataUserInteractionsSeeder extends Seeder
{
    private const USER_EMAIL_DOMAIN = '%@approved-users.test';

    public function run(): void
    {
        $users = User::query()
            ->where('email', 'like', self::USER_EMAIL_DOMAIN)
            ->orderBy('id')
            ->get()
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
            ->orderBy('id')
            ->get()
            ->values();

        $events = Event::query()
            ->where('status', Status::APPROVED)
            ->orderBy('id')
            ->get()
            ->values();

        $acceptedBoothIdValues = $acceptedBoothIds->all();
        $approvedEventIdValues = $events->pluck('id')->values()->all();

        $this->cleanInvalidInteractions($acceptedBoothIdValues, $approvedEventIdValues);

        if ($users->isEmpty() || ($booths->isEmpty() && $events->isEmpty())) {
            $this->command?->warn('RealDataUserInteractionsSeeder skipped: required users or accepted targets are missing.');
            return;
        }

        $base = Carbon::create(2026, 8, 17, 9, 0, 0);
        $comments = [
            'Useful information and a well-presented exhibition presence.',
            'The details were clear and relevant to my visit.',
            'Good experience; I would like to follow up after the exhibition.',
            'Interesting topic and helpful information for visitors.',
            'The presentation was practical and easy to understand.',
        ];

        $this->seedEventInteractions($events, $users, $base, $comments);
        $this->seedBoothInteractions($booths, $users, $base, $comments);
        $this->seedReports($booths, $events, $users, $base);
        $this->seedEventReminders($events, $users, $base);

        $this->command?->info('Seeded expanded weekly interactions for all ordinary users on accepted booths and approved events.');
    }

    /** @param array<int, int> $acceptedBoothIds */
    /** @param array<int, int> $approvedEventIds */
    private function cleanInvalidInteractions(array $acceptedBoothIds, array $approvedEventIds): void
    {
        $this->deleteInvalidTargets('reviews', 'reviewable_type', 'reviewable_id', Booth::class, $acceptedBoothIds);
        if ($acceptedBoothIds !== []) {
            Review::query()->where('reviewable_type', Booth::class)->whereIn('reviewable_id', $acceptedBoothIds)->delete();
        }
        $this->deleteInvalidTargets('reviews', 'reviewable_type', 'reviewable_id', Event::class, $approvedEventIds);
        $this->deleteInvalidTargets('leads', 'leadable_type', 'leadable_id', Booth::class, $acceptedBoothIds);
        $this->deleteInvalidTargets('leads', 'leadable_type', 'leadable_id', Event::class, $approvedEventIds);
        $this->deleteInvalidTargets('saved', 'savedable_type', 'savedable_id', Booth::class, $acceptedBoothIds);
        $this->deleteInvalidTargets('saved', 'savedable_type', 'savedable_id', Event::class, $approvedEventIds);
        $this->deleteInvalidTargets('reports', 'reportable_type', 'reportable_id', Booth::class, $acceptedBoothIds);
        $this->deleteInvalidTargets('reports', 'reportable_type', 'reportable_id', Event::class, $approvedEventIds);

        if ($approvedEventIds === []) {
            EventReminder::query()->delete();
        } else {
            EventReminder::query()->whereNotIn('event_id', $approvedEventIds)->delete();
        }
    }

    /** @param array<int, int> $validIds */
    private function deleteInvalidTargets(string $table, string $typeColumn, string $idColumn, string $type, array $validIds): void
    {
        $query = DB::table($table)->where($typeColumn, $type);
        $validIds === [] ? $query->delete() : $query->whereNotIn($idColumn, $validIds)->delete();
    }

    /** @param Collection<int, Event> $events */
    /** @param Collection<int, User> $users */
    /** @param array<int, string> $comments */
    private function seedEventInteractions(Collection $events, Collection $users, Carbon $base, array $comments): void
    {
        $reviews = [];
        $leads = [];
        $saved = [];

        foreach ($events as $targetIndex => $event) {
            $reviewCount = min(20 + (($targetIndex * 7) % 21), $users->count());
            $savedCount = min(8 + (($targetIndex * 5) % 11), $users->count());
            $leadCount = min(max($savedCount + 4, 12 + (($targetIndex * 3) % 17)), $users->count());

            for ($j = 0; $j < $reviewCount; $j++) {
                $user = $users[(($targetIndex * 20) + $j) % $users->count()];
                $at = $this->timestamp($base, $targetIndex + $j, 9 + (($targetIndex + $j) % 8), ($j * 7) % 60);
                $reviews[$user->id.'|event|'.$event->id] = [
                    'user_id' => $user->id,
                    'reviewable_type' => Event::class,
                    'reviewable_id' => $event->id,
                    'rating' => 3 + (($targetIndex + $j) % 3),
                    'comment' => $comments[($targetIndex + $j) % count($comments)],
                    'created_at' => $at,
                    'updated_at' => $at,
                ];
            }

            for ($j = 0; $j < $leadCount; $j++) {
                $user = $users[(($targetIndex * 12) + $j + 7) % $users->count()];
                $at = $this->timestamp($base, $targetIndex + $j + 1, 10 + ($j % 7));
                $leads[$user->id.'|event|'.$event->id] = [
                    'user_id' => $user->id,
                    'leadable_type' => Event::class,
                    'leadable_id' => $event->id,
                    'created_at' => $at,
                ];
            }

            for ($j = 0; $j < $savedCount; $j++) {
                $user = $users[(($targetIndex * 8) + $j + 13) % $users->count()];
                $at = $this->timestamp($base, $targetIndex + $j + 2, 11 + ($j % 6));
                $saved[$user->id.'|event|'.$event->id] = [
                    'user_id' => $user->id,
                    'savedable_type' => Event::class,
                    'savedable_id' => $event->id,
                    'created_at' => $at,
                ];
            }
        }

        $this->upsertRows('reviews', array_values($reviews), ['user_id', 'reviewable_type', 'reviewable_id'], ['rating', 'comment', 'created_at', 'updated_at']);
        $this->upsertRows('leads', array_values($leads), ['user_id', 'leadable_type', 'leadable_id'], ['created_at']);
        $this->upsertRows('saved', array_values($saved), ['user_id', 'savedable_type', 'savedable_id'], ['created_at']);
    }

    /** @param Collection<int, Booth> $booths */
    /** @param Collection<int, User> $users */
    /** @param array<int, string> $comments */
    private function seedBoothInteractions(Collection $booths, Collection $users, Carbon $base, array $comments): void
    {
        $reviews = [];
        $leads = [];
        $saved = [];

        foreach ($booths as $targetIndex => $booth) {
            $reviewCount = min(15 + (($targetIndex * 5) % 18), $users->count());
            $savedCount = min(20 + (($targetIndex * 5) % 22), $users->count());
            $leadCount = min(max($savedCount + 8, 28 + (($targetIndex * 2) % 30)), $users->count());

            for ($j = 0; $j < $reviewCount; $j++) {
                $user = $users[(($targetIndex * 4) + $j + 3) % $users->count()];
                $at = $this->timestamp($base, $targetIndex + $j, 9 + ($j % 8), ($j * 11) % 60);
                $reviews[$user->id.'|booth|'.$booth->id] = [
                    'user_id' => $user->id,
                    'reviewable_type' => Booth::class,
                    'reviewable_id' => $booth->id,
                    'rating' => 3 + (($targetIndex + $j) % 3),
                    'comment' => $comments[($targetIndex + $j) % count($comments)],
                    'created_at' => $at,
                    'updated_at' => $at,
                ];
            }

            for ($j = 0; $j < $leadCount; $j++) {
                $user = $users[(($targetIndex * 10) + $j + 17) % $users->count()];
                $at = $this->timestamp($base, $targetIndex + $j + 1, 10 + ($j % 7));
                $leads[$user->id.'|booth|'.$booth->id] = [
                    'user_id' => $user->id,
                    'leadable_type' => Booth::class,
                    'leadable_id' => $booth->id,
                    'created_at' => $at,
                ];
            }

            for ($j = 0; $j < $savedCount; $j++) {
                $user = $users[(($targetIndex * 8) + $j + 23) % $users->count()];
                $at = $this->timestamp($base, $targetIndex + $j + 2, 11 + ($j % 6));
                $saved[$user->id.'|booth|'.$booth->id] = [
                    'user_id' => $user->id,
                    'savedable_type' => Booth::class,
                    'savedable_id' => $booth->id,
                    'created_at' => $at,
                ];
            }
        }

        $this->upsertRows('reviews', array_values($reviews), ['user_id', 'reviewable_type', 'reviewable_id'], ['rating', 'comment', 'created_at', 'updated_at']);
        $this->upsertRows('leads', array_values($leads), ['user_id', 'leadable_type', 'leadable_id'], ['created_at']);
        $this->upsertRows('saved', array_values($saved), ['user_id', 'savedable_type', 'savedable_id'], ['created_at']);
    }

    /** @param Collection<int, Booth> $booths */
    /** @param Collection<int, Event> $events */
    /** @param Collection<int, User> $users */
    private function seedReports(Collection $booths, Collection $events, Collection $users, Carbon $base): void
    {
        $targets = $booths->concat($events)->values();
        if ($targets->isEmpty()) {
            return;
        }

        $rows = [];
        for ($i = 0; $i < 90; $i++) {
            $user = $users[($i * 3 + 5) % $users->count()];
            $target = $targets[($i * 5 + 2) % $targets->count()];
            $at = $this->timestamp($base, $i + 1, 10 + ($i % 6));
            $rows[] = [
                'reporter_type' => User::class,
                'reporter_id' => $user->id,
                'reportable_type' => $target::class,
                'reportable_id' => $target->id,
                'title' => 'Visitor feedback: '.$target->getKey(),
                'description' => 'Visitor reported that this information should be reviewed or clarified for a better exhibition experience.',
                'status' => ReportStatus::PENDING,
                'resolved_by' => null,
                'created_at' => $at,
                'updated_at' => $at,
            ];
        }

        foreach ($rows as $row) {
            Report::query()->firstOrCreate([
                'reporter_type' => $row['reporter_type'],
                'reporter_id' => $row['reporter_id'],
                'reportable_type' => $row['reportable_type'],
                'reportable_id' => $row['reportable_id'],
                'title' => $row['title'],
            ], $row);
        }
    }

    /** @param Collection<int, Event> $events */
    /** @param Collection<int, User> $users */
    private function seedEventReminders(Collection $events, Collection $users, Carbon $base): void
    {
        $rows = [];
        $limit = min(180, $events->count() * $users->count());
        for ($i = 0; $i < $limit; $i++) {
            $event = $events[$i % $events->count()];
            $user = $users[($i * 7 + 1) % $users->count()];
            $rows[$event->id.'|'.$user->id] = [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'reminded_at' => $this->timestamp($base, $i % 7, 8 + ($i % 10)),
            ];
        }

        $this->upsertRows('event_reminders', array_values($rows), ['event_id', 'user_id'], ['reminded_at']);
    }

    private function timestamp(Carbon $base, int $dayOffset, int $hour, int $minute = 0): Carbon
    {
        return $base->copy()->addDays($dayOffset % 7)->setTime($hour, $minute);
    }

    /** @param array<int, array<string, mixed>> $rows */
    /** @param array<int, string> $uniqueBy */
    /** @param array<int, string> $updateColumns */
    private function upsertRows(string $table, array $rows, array $uniqueBy, array $updateColumns): void
    {
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table($table)->upsert($chunk, $uniqueBy, $updateColumns);
        }
    }
}
