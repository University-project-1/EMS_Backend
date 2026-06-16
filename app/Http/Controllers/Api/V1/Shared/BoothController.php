<?php

namespace App\Http\Controllers\Api\v1\Shared;

use App\DTOs\SystemUser\BoothUpdateDTO;
use App\Filter\BookedBoothFilter;
use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\UpdateBoothRequest;
use App\Http\Resources\Shared\BoothResource;
use App\Models\Booth;
use App\Services\SystemUser\Admin\UpdateBoothService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BoothController extends Controller
{
    public function __construct(
        private readonly UpdateBoothService $updateBoothService,
    ){}

    public function index(){
        $booths = QueryBuilder::for(Booth::class)
            ->allowedFilters(
                AllowedFilter::exact('number'),
                AllowedFilter::exact('area'),
                AllowedFilter::custom('booked', new BookedBoothFilter()),
                AllowedFilter::custom('min_price', new MinFilter(), 'price'),
                AllowedFilter::custom('max_price', new MaxFilter(), 'price')
            )
            ->allowedIncludes('company', 'hall')
            ->allowedSorts('price', 'area')
            ->paginate(10);
        return successResponse(
            data: BoothResource::collection($booths),
            message: 'booths returned successfully',
        );
    }

    public function show(Booth $booth){
        $booth->loadMissing(['hall', 'company']);
        return successResponse(
            data: new BoothResource($booth),
            message: 'booth returned successfully',
        );
    }

    public function update(Booth $booth, UpdateBoothRequest $request){
        $dto = BoothUpdateDTO::fromRequest($request->validated());
        $updatedBooth = $this->updateBoothService->update($booth, $dto);

        return successResponse(
            data: new BoothResource($updatedBooth),
            message: 'booth updated successfully',
        );
    }
}

