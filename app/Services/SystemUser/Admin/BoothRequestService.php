<?php

namespace App\Services\SystemUser\Admin;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Notifications\SystemUser\BoothApprovedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BoothRequestService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function getConflictingRequests(BoothRequest $request){
        return $boothRequests = BoothRequest::where('id','!=', $request->id)
            ->where('booth_id', $request->booth_id)
            ->where('status', Status::PENDING)
            ->with(['company', 'company.logoMedia'])
            ->paginate(3);
    }

    public function approve(BoothRequest $boothRequest){
        if($boothRequest->status !== Status::PENDING){
            throw new HttpException(400, __('booth.invalid_status'));
        }
        return DB::transaction(function() use ($boothRequest){
            $boothRequest->update(['status' => Status::APPROVED]);
            Booth::where('id', $boothRequest->booth_id)
            ->update([
                'company_id' => $boothRequest->company_id,
                'qr_token'=>'B-' . $boothRequest->booth_id . '-' . Str::random(10),
            ]);

            BoothRequest::where('id', '!=', $boothRequest->id)
                ->where('booth_id', $boothRequest->booth_id)
                ->where('status', Status::PENDING)
                ->update(['status' => Status::REJECTED]);

            $boothRequest->systemUser->notify(new BoothApprovedNotification($boothRequest));
        });
    }

    public function reject(BoothRequest $boothRequest){
        if($boothRequest->status !== Status::PENDING){
            throw new HttpException(400, __('booth.invalid_status'));
        }
        return $boothRequest->update(['status' => Status::REJECTED]);
    }
}
