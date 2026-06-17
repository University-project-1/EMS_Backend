<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Shared;

use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\HallResource;
use App\Models\Hall;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class HallController extends Controller
{
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
            ->paginate(2);

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
