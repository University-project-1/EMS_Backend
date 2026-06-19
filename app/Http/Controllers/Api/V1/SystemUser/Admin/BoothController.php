<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\BoothUpdateDTO;
use App\Filter\BookedBoothFilter;
use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\UpdateBoothRequest;
use App\Http\Resources\SystemUser\Shared\BoothResource;
use App\Models\Booth;
use App\Services\SystemUser\Admin\UpdateBoothService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/Booths')]
class BoothController extends Controller
{
    public function __construct(
        private readonly UpdateBoothService $updateBoothService,
    ){}

    /**
     * all
     */
    #[QueryParameter('filter[number]', 'Filter booths by exact booth number', required: false, type: 'string')]
    #[QueryParameter('filter[booked]', 'Filter booths by booking status', required: false, type: 'boolean')]
    #[QueryParameter('filter[min_price]', 'Filter booths by minimum price', required: false, type: 'number')]
    #[QueryParameter('filter[max_price]', 'Filter booths by maximum price', required: false, type: 'number')]
    #[QueryParameter('filter[min_area]', 'Filter booths by minimum area', required: false, type: 'number')]
    #[QueryParameter('filter[max_area]', 'Filter booths by maximum area', required: false, type: 'number')]
    #[QueryParameter('include', 'Include related resources (company, hall)', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort results by field (price, area). Prefix with - for descending order', required: false, type: 'string')]
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

    /**
     * update
     */
    public function update(Booth $booth, UpdateBoothRequest $request){
        $dto = BoothUpdateDTO::fromRequest($request->validated());
        $updatedBooth = $this->updateBoothService->update($booth, $dto);

        return successResponse(
            data: new BoothResource($updatedBooth),
            message: 'booth updated successfully',
        );
    }
}

