<?php

namespace App\Http\Controllers\Api\v1\SystemUser\Exhibitor;

use App\Filter\BookedBoothFilter;
use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Shared\BoothResource;
use App\Models\Booth;
use Dedoc\Scramble\Attributes\Group;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Exhibitor/Booth')]
class BoothController extends Controller
{
    /**
     * all
     */
    public function index(){
        $booths = QueryBuilder::for(Booth::class)
            ->allowedFilters(
                AllowedFilter::exact('number'),
                AllowedFilter::custom('booked', new BookedBoothFilter()),
                AllowedFilter::custom('min_price', new MinFilter(), 'price'),
                AllowedFilter::custom('max_price', new MaxFilter(), 'price'),
                AllowedFilter::custom('min_area', new MinFilter(), 'area'),
                AllowedFilter::custom('max_area', new MaxFilter(), 'area')
            )
            ->allowedIncludes('company', 'hall')
            ->allowedSorts('price', 'area')
            ->paginate(10);
        return successResponse(
            data: BoothResource::collection($booths),
            message: 'booths returned successfully',
        );
    }

    /**
     * show
     */
    public function show(Booth $booth){
        $booth->loadMissing(['hall', 'company']);
        return successResponse(
            data: new BoothResource($booth),
            message: 'booth returned successfully',
        );
    }
}
