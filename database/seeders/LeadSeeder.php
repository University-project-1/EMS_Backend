<?php

namespace Database\Seeders;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->whereIn('email', [
                'wasemalhariri13@gmail.com',
                'mehyarkhuder11e@gmail.com',
                'mzyalnoun@gmail.com',
            ])
            ->get()
            ->keyBy('email');

        $booths = Booth::query()
            ->whereNotNull('company_id')
            ->whereNotNull('qr_token')
            ->whereHas('company', fn ($query) => $query->where('status', Status::APPROVED))
            ->whereHas('boothRequests', function ($query): void {
                $query
                    ->where('status', Status::APPROVED->value)
                    ->whereColumn('booth_requests.company_id', 'booths.company_id');
            })
            ->get()
            ->keyBy('number');

        $events = Event::query()
            ->where('status', Status::APPROVED)
            ->whereNotNull('qr_token')
            ->get()
            ->keyBy('title');

        $leadDefinitions = [
            ['user' => 'wasemalhariri13@gmail.com', 'leadable' => $booths['25B-01'] ?? null, 'days_ago' => 12, 'hour' => 9],
            ['user' => 'mehyarkhuder11e@gmail.com', 'leadable' => $booths['2C-01'] ?? null, 'days_ago' => 11, 'hour' => 10],
            ['user' => 'mzyalnoun@gmail.com', 'leadable' => $booths['10D-01'] ?? null, 'days_ago' => 10, 'hour' => 11],
            ['user' => 'wasemalhariri13@gmail.com', 'leadable' => $booths['11F-01'] ?? null, 'days_ago' => 9, 'hour' => 12],
            ['user' => 'mehyarkhuder11e@gmail.com', 'leadable' => $booths['25B-02'] ?? null, 'days_ago' => 8, 'hour' => 13],
            ['user' => 'mzyalnoun@gmail.com', 'leadable' => $booths['26E-01'] ?? null, 'days_ago' => 7, 'hour' => 14],
            ['user' => 'wasemalhariri13@gmail.com', 'leadable' => $booths['26E-02'] ?? null, 'days_ago' => 6, 'hour' => 15],
            ['user' => 'mehyarkhuder11e@gmail.com', 'leadable' => $booths['36JD-01'] ?? null, 'days_ago' => 5, 'hour' => 16],
            ['user' => 'mzyalnoun@gmail.com', 'leadable' => $booths['36JD-02'] ?? null, 'days_ago' => 4, 'hour' => 17],
            ['user' => 'wasemalhariri13@gmail.com', 'leadable' => $events['Building Scalable Laravel Applications'] ?? null, 'days_ago' => 3, 'hour' => 10],
            ['user' => 'mehyarkhuder11e@gmail.com', 'leadable' => $events['The Future of Publishing'] ?? null, 'days_ago' => 3, 'hour' => 11],
            ['user' => 'mzyalnoun@gmail.com', 'leadable' => $events['Modern API Security'] ?? null, 'days_ago' => 2, 'hour' => 12],
            ['user' => 'wasemalhariri13@gmail.com', 'leadable' => $events['Creative Reading Experiences'] ?? null, 'days_ago' => 2, 'hour' => 13],
            ['user' => 'mehyarkhuder11e@gmail.com', 'leadable' => $events['Building Scalable Laravel Applications'] ?? null, 'days_ago' => 1, 'hour' => 14],
            ['user' => 'mzyalnoun@gmail.com', 'leadable' => $events['The Future of Publishing'] ?? null, 'days_ago' => 1, 'hour' => 15],
        ];

        foreach ($leadDefinitions as $definition) {
            $user = $users->get($definition['user']);
            $leadable = $definition['leadable'];

            if (! $user instanceof User || ! $leadable instanceof Booth && ! $leadable instanceof Event) {
                continue;
            }

            $lead = Lead::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'leadable_type' => $leadable::class,
                    'leadable_id' => $leadable->getKey(),
                ],
                []
            );

            $lead->forceFill([
                'created_at' => Carbon::now()->subDays($definition['days_ago'])->setTime($definition['hour'], 0),
            ])->saveQuietly();
        }
    }
}
