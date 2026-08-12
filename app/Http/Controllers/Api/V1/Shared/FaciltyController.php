<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\FaciltyResource;
use App\Models\Facility;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Facilities')]
class FaciltyController extends Controller
{
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
