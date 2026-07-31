<?php

namespace App\Services\SystemUser\Admin;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Notifications\SystemUser\BoothApprovedNotification;
use App\Services\Shared\QrCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BoothRequestService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly QrCodeService $qrCodeService
    ) {}

    public function getConflictingRequests(BoothRequest $request)
    {
        return $boothRequests = BoothRequest::where('id', '!=', $request->id)
            ->where('booth_id', $request->booth_id)
            ->where('status', Status::PENDING)
            ->with(['company', 'company.logoMedia'])
            ->paginate(3);
    }

    public function approve(BoothRequest $boothRequest)
    {
        if ($boothRequest->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        // Eager load the company and its system users to prevent N+1 queries
        $boothRequest->load('company.systemUsers');

        DB::transaction(function () use ($boothRequest) {
            $boothRequest->update(['status' => Status::APPROVED]);

            $boothRequest->company->update(['status' => Status::APPROVED]);

            $token = 'B-'.$boothRequest->booth_id.'-'.Str::random(10);

            $booth = Booth::findOrFail($boothRequest->booth_id);
            $booth->update([
                'company_id' => $boothRequest->company_id,
                'qr_token' => $token,
            ]);

            BoothRequest::where('id', '!=', $boothRequest->id)
                ->where('booth_id', $boothRequest->booth_id)
                ->where('status', Status::PENDING)
                ->update(['status' => Status::REJECTED]);

            $booth->addMediaFromString($this->qrCodeService->generateSvg($token))
                ->usingFileName("{$token}.svg")
                ->toMediaCollection('qr_code');
        });

        $usersToNotify = $boothRequest->company->systemUsers;

        if (! $usersToNotify->contains('id', $boothRequest->system_user_id)) {
            $usersToNotify->push($boothRequest->systemUser);
        }

        Notification::send($usersToNotify->unique('id'), new BoothApprovedNotification($boothRequest));

        return $boothRequest;
    }

    public function reject(BoothRequest $boothRequest)
    {
        if ($boothRequest->status !== Status::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        return $boothRequest->update(['status' => Status::REJECTED]);
    }
}
