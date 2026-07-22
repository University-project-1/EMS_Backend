<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\BoothResource;
use App\Models\Booth;
use App\Models\Event;
use App\Services\Mobile\SavedService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;

#[Group('Visitor/Saved')]
class SavedController extends Controller
{
    public function __construct(protected SavedService $savedService){}

    /**
     * toggle event saved state.
     */
    public function toggleEvent(Event $event){
        $this->savedService->toggleSave($event);

        return successResponse();
    }

    /**
     * toggle booth saved state.
     */
    public function toggleBooth(Booth $booth){
        $this->savedService->toggleSave($booth);

        return successResponse();
    }

    /**
     * saved booths.
     */
    #[QueryParameter('filter[search]', type: 'string', description: 'Search booths by company name.')]
    #[QueryParameter('filter[business_sector]','Filter by company business sector.',required: false,type: 'string')]
    #[QueryParameter('include','Include related resources (company,hall,company.logoMedia).',required: false,type: 'string')]
    #[QueryParameter('per_page','Number of items per page. Default: 15, Max: 100.',required: false,type: 'integer')]
    public function savedBooths(){
        $perPage = min(max(request()->integer('per_page', 15), 1), 100);

        $booths = $this->savedService->savedBooths($perPage);

        return successResponse(BoothResource::collection($booths));
    }
}