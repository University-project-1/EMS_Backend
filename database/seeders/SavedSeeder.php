<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Saved;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavedSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->get();
        $events = Event::query()->where('status', Status::APPROVED)->orderByDesc('id')->take(2)->get();
        $booths = Booth::query()->whereNotNull('company_id')->orderByDesc('id')->take(2)->get();

        foreach ($users as $user) {
            foreach ($events as $event) {
                Saved::query()->firstOrCreate([
                    'user_id' => $user->getKey(),
                    'savedable_type' => Event::class,
                    'savedable_id' => $event->getKey(),
                ]);
            }

            foreach ($booths as $booth) {
                Saved::query()->firstOrCreate([
                    'user_id' => $user->getKey(),
                    'savedable_type' => Booth::class,
                    'savedable_id' => $booth->getKey(),
                ]);
            }
        }
    }
}
