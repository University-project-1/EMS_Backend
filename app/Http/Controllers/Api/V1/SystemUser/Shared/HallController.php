<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Shared;

use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\HallResource;
use App\Models\Hall;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class HallController extends Controller
{
    #[QueryParameter('filter[type]', 'Filter halls by exact type', required: false, type: 'string')]
    #[QueryParameter('filter[number]', 'Filter halls by exact number', required: false, type: 'string')]
    #[QueryParameter('filter[min_area]', 'Filter halls by minimum area', required: false, type: 'number')]
    #[QueryParameter('filter[max_area]', 'Filter halls by maximum area', required: false, type: 'number')]
    #[QueryParameter('include', 'Include related resources (booths)', required: false, type: 'string')]
    #[QueryParameter('fields', 'Specify fields to return for related resources (e.g., fields[booths]=id,hall_id,number)', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort results by field (area). Prefix with - for descending order', required: false, type: 'string')]
    public function index()
    {
        $halls = QueryBuilder::for(Hall::class)
            ->allowedFilters(
                AllowedFilter::exact('type'),
                AllowedFilter::exact('number'),
                AllowedFilter::custom('min_area', new MinFilter(), 'area'),
                AllowedFilter::custom('max_area', new MaxFilter(), 'area'),
            )
            ->allowedIncludes('booths')
            ->allowedFields('booths.id', 'booths.hall_id', 'booths.number')
            ->allowedSorts('area')
            ->get();

        return successResponse(
            data: HallResource::collection($halls),
            message: 'Halls returned successfully',
        );
    }

    public function show(Hall $hall)
    {
        $hall->load('booths');

        return successResponse(
            data: new HallResource($hall),
            message: 'Hall returned successfully',
        );
    }

}
