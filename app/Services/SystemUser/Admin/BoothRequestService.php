<?php

namespace App\Services\SystemUser\Admin;

use App\Enum\RequestRejectionReason;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Notifications\SystemUser\Exhibitor\BoothPaymentReminderNotification;
use App\Notifications\SystemUser\Exhibitor\BoothRequestStatusNotification;
use App\Services\Shared\NotificationRecipientResolver;
use App\Services\Shared\QrCodeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BoothRequestService
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
        private readonly NotificationRecipientResolver $notificationRecipients,
    ) {}

    public function getConflictingRequests(BoothRequest $request)
    {
        return BoothRequest::query()
            ->where('id', '!=', $request->id)
            ->where('booth_id', $request->booth_id)
            ->where('status', Status::PENDING)
            ->with(['company', 'company.logoMedia'])
            ->paginate(3);
    }

    public function approve(BoothRequest $boothRequest): BoothRequest
    {
        DB::transaction(function () use ($boothRequest): void {
            $booth = Booth::query()->whereKey($boothRequest->booth_id)->lockForUpdate()->firstOrFail();
            $boothRequest->refresh()->load('company.systemUsers');

            if ($boothRequest->status !== Status::PENDING) {
                throw new HttpException(400, __('validation.invalid_status'));
            }

            $conflictingRequests = BoothRequest::query()
                ->where('id', '!=', $boothRequest->id)
                ->where('booth_id', $boothRequest->booth_id)
                ->where('status', Status::PENDING->value)
                ->with(['company.systemUsers', 'systemUser'])
                ->get();

            $boothRequest->update(['status' => Status::APPROVED]);
            $boothRequest->company->update(['status' => Status::APPROVED]);

            $token = 'B-'.$boothRequest->booth_id.'-'.Str::random(10);
            $booth->update([
                'company_id' => $boothRequest->company_id,
                'qr_token' => $token,
            ]);

            $conflictingRequests->each(
                fn (BoothRequest $conflictingRequest) => $conflictingRequest->update(['status' => Status::REJECTED])
            );

            $booth->clearMediaCollection('qr_code');
            Storage::disk('public')->deleteDirectory('booths/'.$booth->id.'/qr_code');
            $booth->addMediaFromString($this->qrCodeService->generateSvg($token))
                ->usingFileName("{$token}.svg")
                ->toMediaCollection('qr_code');

            DB::afterCommit(function () use ($boothRequest, $conflictingRequests): void {
                Notification::send(
                    $this->notificationRecipients->boothRequestRecipients($boothRequest),
                    new BoothRequestStatusNotification($boothRequest, Status::APPROVED),
                );

                $this->notifyConflictingRequestRecipients($conflictingRequests);
            });
        });

        return $boothRequest;
    }

    public function sendPaymentReminder(BoothRequest $boothRequest): void
    {
        if ($boothRequest->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        $boothRequest->loadMissing(['systemUser', 'booth']);

        Notification::send($boothRequest->systemUser, new BoothPaymentReminderNotification($boothRequest));
    }

    public function reject(BoothRequest $boothRequest)
    {
        if ($boothRequest->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        return DB::transaction(function () use ($boothRequest) {
            $updated = $boothRequest->update(['status' => Status::REJECTED]);

            DB::afterCommit(function () use ($boothRequest): void {
                Notification::send(
                    $this->notificationRecipients->boothRequestRecipients($boothRequest),
                    new BoothRequestStatusNotification($boothRequest, Status::REJECTED),
                );
            });

            return $updated;
        });
    }

    public function cancelApprovedBooking(Booth $booth): BoothRequest
    {
        return DB::transaction(function () use ($booth): BoothRequest {
            $booth = Booth::query()
                ->whereKey($booth->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $boothRequest = BoothRequest::query()
                ->where('booth_id', $booth->getKey())
                ->where('status', Status::APPROVED->value)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($boothRequest === null || (int) $booth->company_id !== (int) $boothRequest->company_id) {
                throw new HttpException(400, __('validation.invalid_status'));
            }

            $boothRequest->update(['status' => Status::CANCELED]);
            $booth->update([
                'company_id' => null,
                'qr_token' => null,
            ]);

            $booth->clearMediaCollection('qr_code');
            Storage::disk('public')->deleteDirectory('booths/'.$booth->id.'/qr_code');

            return $boothRequest;
        });
    }

    private function notifyConflictingRequestRecipients(Collection $boothRequests): void
    {
        $boothRequests->each(function (BoothRequest $boothRequest): void {
            Notification::send(
                $this->notificationRecipients->boothRequestRecipients($boothRequest),
                new BoothRequestStatusNotification(
                    $boothRequest,
                    Status::REJECTED,
                    RequestRejectionReason::BOOTH_CONFLICT,
                ),
            );
        });
    }
}
