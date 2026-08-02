<?php

namespace App\Services\SystemUser\Admin;

use App\Enum\Status;
use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Services\Shared\QrCodeService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventRequestService
{
    public function __construct(
        private QrCodeService $qrCodeService
    ){}
    public function getConflictingRequests(Event $event): LengthAwarePaginator
    {
        return Event::query()
            ->where('id', '!=', $event->id)
            ->where('event_hall_id', $event->event_hall_id)
            ->where('status', Status::PENDING->value)
            ->where('start_at', '<', $event->end_at)
            ->where('end_at', '>', $event->start_at)
            ->with('media', 'eventable')
            ->paginate(perPage: 3, page: 1);
    }

    public function approve(Event $event): void
    {
        DB::transaction(function () use ($event): void {
            EventHall::query()->whereKey($event->event_hall_id)->lockForUpdate()->firstOrFail();
            $event->refresh();

            if ($event->status !== Status::PENDING) {
                throw new HttpException(400, __('validation.invalid_status'));
            }

            $hasApprovedConflict = Event::query()
                ->where('id', '!=', $event->id)
                ->where('event_hall_id', $event->event_hall_id)
                ->where('status', Status::APPROVED->value)
                ->where('start_at', '<', $event->end_at)
                ->where('end_at', '>', $event->start_at)
                ->exists();

            if ($hasApprovedConflict) {
                throw new HttpException(409, __('validation.hall_unavailable'));
            }
            $token = 'E-'.$event->id.'-'.Str::random(10);
            $event->update([
                'status' => Status::APPROVED,
                'qr_token' => $token,
            ]);
            $event->addMediaFromString($this->qrCodeService->generateSvg($token))
                ->usingFileName("{$token}.svg")
                ->toMediaCollection('qr_code');
            if ($event->eventable_type === Company::class) {
                $event->eventable->update(['status' => Status::APPROVED]);
            }

            Event::query()->where('id', '!=', $event->id)
                ->where('event_hall_id', $event->event_hall_id)
                ->where('status', Status::PENDING->value)
                ->where('start_at', '<', $event->end_at)
                ->where('end_at', '>', $event->start_at)
                ->update(['status' => Status::REJECTED]);
        });
    }

    public function reject(Event $event): void
    {
        if ($event->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        $event->update(['status' => Status::REJECTED]);
    }
}
