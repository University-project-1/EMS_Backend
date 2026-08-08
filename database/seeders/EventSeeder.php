<?php

namespace Database\Seeders;

use App\Enum\EventType;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\SystemUser;
use App\Services\Shared\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * The M labels are independent event halls. Areas missing from the map are
     * stable estimates, and prices follow the existing 500-per-square-metre rate.
     *
     * @var list<array{number: string, area: float, svg_id: string, price_per_hour: float}>
     */
    private const EVENT_HALLS = [
        ['number' => 'M1', 'area' => 120.0, 'svg_id' => 'event-hall-m1', 'price_per_hour' => 60000.0],
        ['number' => 'M2', 'area' => 100.0, 'svg_id' => 'event-hall-m2', 'price_per_hour' => 50000.0],
        ['number' => 'M3', 'area' => 288.0, 'svg_id' => 'event-hall-m3', 'price_per_hour' => 144000.0],
        ['number' => 'M3.1', 'area' => 76.0, 'svg_id' => 'event-hall-m3-1', 'price_per_hour' => 38000.0],
        ['number' => 'M3.2', 'area' => 75.0, 'svg_id' => 'event-hall-m3-2', 'price_per_hour' => 37500.0],
        ['number' => 'M4', 'area' => 504.0, 'svg_id' => 'event-hall-m4', 'price_per_hour' => 252000.0],
        ['number' => 'M5', 'area' => 65.0, 'svg_id' => 'event-hall-m5', 'price_per_hour' => 32500.0],
        ['number' => 'M6', 'area' => 675.0, 'svg_id' => 'event-hall-m6', 'price_per_hour' => 337500.0],
        ['number' => 'M6.1', 'area' => 188.0, 'svg_id' => 'event-hall-m6-1', 'price_per_hour' => 94000.0],
        ['number' => 'M7', 'area' => 390.0, 'svg_id' => 'event-hall-m7', 'price_per_hour' => 195000.0],
        ['number' => 'M8', 'area' => 140.0, 'svg_id' => 'event-hall-m8', 'price_per_hour' => 70000.0],
        ['number' => 'M9', 'area' => 60.0, 'svg_id' => 'event-hall-m9', 'price_per_hour' => 30000.0],
        ['number' => 'M10', 'area' => 100.0, 'svg_id' => 'event-hall-m10', 'price_per_hour' => 50000.0],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventHall::query()
            ->whereIn('number', ['1', '2', '3', '4'])
            ->whereDoesntHave('events')
            ->delete();

        foreach (self::EVENT_HALLS as $eventHallData) {
            EventHall::query()->updateOrCreate(
                ['number' => $eventHallData['number']],
                $eventHallData,
            );
        }

        $halls = EventHall::query()
            ->whereIn('number', collect(self::EVENT_HALLS)->pluck('number'))
            ->pluck('id', 'number');

        $elcoach = SystemUser::query()->where('name', 'Elcoach')->firstOrFail();
        $companyA = Company::query()->where('name', 'Dar Al feker')->firstOrFail();
        $companyB = Company::query()->where('name', 'GreenFoods Co.')->firstOrFail();

        $events = [
            [
                'title' => 'Building Scalable Laravel Applications',
                'eventable' => $elcoach,
                'event_hall_id' => $halls['M1'],
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
                'event_hall_id' => $halls['M2'],
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
                'event_hall_id' => $halls['M3'],
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
                'event_hall_id' => $halls['M4'],
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
                'event_hall_id' => $halls['M5'],
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

            if ($event->status === Status::APPROVED && $event->eventable_type === Company::class) {
                $event->loadMissing('eventable');

                if ($event->eventable instanceof Company) {
                    $event->eventable->update(['status' => Status::APPROVED]);
                }
            }

            $this->syncQrCodeMedia($event);
        }
    }

    private function syncQrCodeMedia(Event $event): void
    {
        $event->clearMediaCollection('qr_code');

        if ($event->status !== Status::APPROVED) {
            return;
        }

        $token = $event->qr_token ?? 'E-SEED-'.Str::slug($event->title);

        if ($event->qr_token !== $token) {
            $event->forceFill(['qr_token' => $token])->saveQuietly();
        }

        $event->addMediaFromString(app(QrCodeService::class)->generateSvg($token))
            ->usingFileName("{$token}.svg")
            ->toMediaCollection('qr_code');
    }
}
