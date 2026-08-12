<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\FaciltyResource;
use App\Models\Facility;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Facilities')]
class FaciltyController extends Controller
{
    #[QueryParameter('filter[number]', 'Filter facilities by exact number', required: false, type: 'string')]
    #[QueryParameter('filter[type]', 'Filter facilities by exact type', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Default: 10', required: false, type: 'integer')]
    public function index(){
        $facilities = QueryBuilder::for(Facility::class)
            ->allowedFilters('number',
                AllowedFilter::exact('type', 'gender')
            )
            ->paginate(Request()->query('per_page', 10));
        return successResponse(FaciltyResource::collection($facilities));
    }

    public function show(Facility $facility){
        return successResponse(new FaciltyResource($facility));
    }
}
