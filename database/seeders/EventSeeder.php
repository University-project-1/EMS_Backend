<?php

namespace Database\Seeders;

use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventHallOne = EventHall::updateOrCreate(['number' => 1], [
            'number' => 1,
            'area' => 100.0,
            'svg_id' => 'event-a01',
            'price_per_hour' => 50000,
        ]);

        $eventHallTwo = EventHall::updateOrCreate(['number' => 2], [
            'number' => 2,
            'area' => 150.0,
            'svg_id' => 'event-a02',
            'price_per_hour' => 75000,
        ]);

        $eventHallThree = EventHall::updateOrCreate(['number' => 3], [
            'number' => 3,
            'area' => 100.0,
            'svg_id' => 'event-a03',
            'price_per_hour' => 50000,
        ]);

        $eventHallFour = EventHall::updateOrCreate(['number' => 4], [
            'number' => 4,
            'area' => 200.0,
            'svg_id' => 'event-a04',
            'price_per_hour' => 100000.00,
        ]);

        $elcoach = SystemUser::query()->where('name', 'Elcoach')->firstOrFail();
        $companyA = Company::query()->where('name', 'Dar Al feker')->firstOrFail();
        $companyB = Company::query()->where('name', 'GreenFoods Co.')->firstOrFail();

        $events = [
            [
                'title' => 'Building Scalable Laravel Applications',
                'eventable' => $elcoach,
                'event_hall_id' => $eventHallOne->id,
                'type' => EventType::WORKSHOP,
                'status' => Status::APPROVED,
                'start_at' => Carbon::now()->addDays(1)->setTime(10, 0),
                'duration' => 2,
                'description' => 'A practical workshop about designing and scaling modern Laravel applications.',
                'qr_token' => 'E-SEED-001',
                'speakers' => ['Elcoach', 'Fawzy'],
            ],
            [
                'title' => 'Modern API Security',
                'eventable' => $elcoach,
                'event_hall_id' => $eventHallTwo->id,
                'type' => EventType::LECTURE,
                'status' => Status::PENDING,
                'start_at' => Carbon::now()->addDays(2)->setTime(12, 0),
                'duration' => 1,
                'description' => 'An introduction to practical API authentication and authorization patterns.',
                'qr_token' => null,
                'speakers' => ['Elcoach'],
            ],
            [
                'title' => 'The Future of Publishing',
                'eventable' => $companyA,
                'event_hall_id' => $eventHallThree->id,
                'type' => EventType::CONFERENCE,
                'status' => Status::APPROVED,
                'start_at' => Carbon::now()->addDays(3)->setTime(11, 0),
                'duration' => 3,
                'description' => 'A conference exploring digital transformation in publishing and exhibitions.',
                'qr_token' => 'E-SEED-002',
                'speakers' => ['Fawzy', 'Elcoach'],
            ],
            [
                'title' => 'Sustainable Food Production',
                'eventable' => $companyB,
                'event_hall_id' => $eventHallFour->id,
                'type' => EventType::LECTURE,
                'status' => Status::REJECTED,
                'start_at' => Carbon::now()->addDays(4)->setTime(14, 0),
                'duration' => 2,
                'description' => 'A discussion about sustainable production and regional food distribution.',
                'qr_token' => null,
                'speakers' => ['Elza3eem'],
            ],
            [
                'title' => 'Creative Reading Experiences',
                'eventable' => $companyA,
                'event_hall_id' => $eventHallOne->id,
                'type' => EventType::OTHER,
                'status' => Status::PENDING,
                'start_at' => Carbon::now()->addDays(5)->setTime(16, 0),
                'duration' => 2,
                'description' => 'An interactive session about creating engaging reading experiences.',
                'qr_token' => null,
                'speakers' => ['Fawzy'],
            ],
        ];

        foreach ($events as $eventData) {
            $startAt = $eventData['start_at'];
            $eventable = $eventData['eventable'];
            $speakers = $eventData['speakers'];

            $event = Event::query()->updateOrCreate(
                ['title' => $eventData['title']],
                [
                    'eventable_type' => $eventable::class,
                    'eventable_id' => $eventable->getKey(),
                    'event_hall_id' => $eventData['event_hall_id'],
                    'type' => $eventData['type'],
                    'status' => $eventData['status'],
                    'qr_token' => $eventData['qr_token'],
                    'start_at' => $startAt,
                    'end_at' => $startAt->copy()->addHours($eventData['duration']),
                    'duration' => $eventData['duration'],
                    'description' => $eventData['description'],
                ],
            );

            $event->speakers()->delete();
            $event->speakers()->createMany(
                array_map(fn (string $speaker): array => ['name' => $speaker], $speakers)
            );
        }
    }
}
