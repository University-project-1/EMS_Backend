<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $events = Event::where('status', Status::APPROVED)->get();
        $booths = Booth::query()
            ->whereNotNull('company_id')
            ->whereHas('boothRequests', function ($query): void {
                $query
                    ->where('status', Status::APPROVED->value)
                    ->whereColumn('booth_requests.company_id', 'booths.company_id');
            })
            ->get();

        if ($users->isEmpty() || ($events->isEmpty() && $booths->isEmpty())) {
            return;
        }

        $reviewables = $events
            ->concat($booths)
            ->shuffle();

        foreach ($reviewables as $reviewable) {
            $reviewersCount = min(
                $users->count(),
                random_int(1, min(5, $users->count()))
            );

            $reviewers = $users->random($reviewersCount);

            foreach ($reviewers as $user) {
                Review::query()->updateOrCreate(
                    [
                        'user_id' => $user->getKey(),
                        'reviewable_type' => $reviewable::class,
                        'reviewable_id' => $reviewable->getKey(),
                    ],
                    [
                        'rating' => random_int(1, 5),
                        'comment' => fake()->optional(0.8)->sentence(
                            random_int(8, 15)
                        ),
                    ],
                );
            }
        }
    }
}
