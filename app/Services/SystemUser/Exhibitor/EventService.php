<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\CompanyDTO;
use App\DTOs\SystemUser\EventDTO;
use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\SystemUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly CompanyService $companyService,
    ) {}

    public function store(SystemUser $user, EventDTO $dto, ?CompanyDTO $companyDto): Event
    {
        return DB::transaction(function () use ($user, $dto, $companyDto) {

            $eventable = $user;
            $eventableType = SystemUser::class;

            if ($companyDto) {
                $eventable = $this->companyService->create($user, $companyDto);
                $eventableType = Company::class;
            } elseif ($dto->companyId) {
                $eventable = Company::findOrFail($dto->companyId);
                $eventableType = Company::class;
            }

            $start = Carbon::parse($dto->start_at);
            $end = $start->copy()->addHours($dto->duration);

            $event = Event::create([
                'eventable_type' => $eventableType,
                'eventable_id' => $eventable->id,
                'event_hall_id' => $dto->eventHallId,
                'type' => $dto->type,
                'status' => Status::PENDING,
                'start_at' => $start,
                'end_at' => $end,
                'duration' => $dto->duration,
                'title' => $dto->title,
                'description' => $dto->description,
            ]);

            $event->speakers()->createMany(
                array_map(fn ($s) => ['name' => $s['name']], $dto->speakers)
            );

            if ($dto->logo !== null) {
                $event->addMedia($dto->logo)->toMediaCollection('event-logo');
            }

            return $event->load('media', 'speakers', 'eventable');
        });
    }
}
