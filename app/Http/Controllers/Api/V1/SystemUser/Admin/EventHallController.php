<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\UpdateEventHallDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\UpdateEventHallRequest;
use App\Http\Resources\Shared\EventHallResource;
use App\Models\EventHall;
use App\Services\SystemUser\Admin\EventHallService;


class EventHallController extends Controller
{
    public function __construct(protected EventHallService $eventHallService){}

    /**
     * update
     */
    public function update(EventHall $eventHall,UpdateEventHallRequest $request){
        $updatedEventHall = $this->eventHallService->update(
            $eventHall, 
            UpdateEventHallDTO::fromRequest($request->validated())
        );

        return successResponse(EventHallResource::make($updatedEventHall));
    }
}
