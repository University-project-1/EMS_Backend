<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\DTOs\SystemUser\CompanyDTO;
use App\DTOs\SystemUser\EventDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Exhibitor\StoreEventRequest;
use App\Http\Resources\Shared\EventResource;
use App\Services\SystemUser\Exhibitor\EventService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('SystemUser/Exhibitor/Events')]
class EventController extends Controller
{
    public function __construct(protected EventService $eventService){}

    /**
     * booking event hall
     */
    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        $eventDto = EventDTO::fromRequest($validated);

        $companyDto = isset($validated['new_company'])
            ? CompanyDTO::fromRequest($validated['new_company'])
            : null;

        $event = $this->eventService->store(
            auth('system')->user(),
            $eventDto,
            $companyDto
        );

        return successResponse(EventResource::make($event));
    }
}
