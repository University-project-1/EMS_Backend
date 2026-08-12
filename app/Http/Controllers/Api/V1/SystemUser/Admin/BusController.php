<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\BusCatalogDTO;
use App\DTOs\SystemUser\UpdateBusCatalogDTO;
use App\Filter\FromDateFilter;
use App\Filter\ToDateFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\StoreBusCatalogRequest;
use App\Http\Requests\SystemUser\Admin\UpdateBusCatalogRequest;
use App\Http\Resources\Shared\BusCatalogResource;
use App\Models\BusCatalog;
use App\Services\SystemUser\Admin\BusCatalogService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/Buses')]
class BusController extends Controller
{
    public function __construct(
        protected readonly BusCatalogService $busCatalogService
    ){}

    #[QueryParameter('filter[location]', 'Filter catalogs partially by location', required: false, type: 'string')]
    #[QueryParameter('filter[start_time]', 'Filter by start time (From) e.g., 14:00', required: false, type: 'string')]
    #[QueryParameter('filter[end_time]', 'Filter by end time (To) e.g., 18:00', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Default: 5', required: false, type: 'integer')]
    /**
     * all
     */
    public function index(){
        $catalog = QueryBuilder::for(BusCatalog::class)
            ->allowedFilters('location',
                AllowedFilter::custom('start_time', new FromDateFilter(),'start_time'),
                AllowedFilter::custom('end_time', new ToDateFilter(), 'end_time')
            )
            ->defaultSort('location')
            ->paginate(Request()->query('per_page', 5));

        return successResponse(BusCatalogResource::collection($catalog));
    }
    /**
     * show
     */
    public function show(BusCatalog $busCatalog){
        return successResponse(new BusCatalogResource($busCatalog));
    }
    /**
     * create
     */
    public function create(StoreBusCatalogRequest $request){
        $dto = BusCatalogDTO::fromRequest($request->validated());
        $bus = $this->busCatalogService->create($dto);
        return successResponse(new BusCatalogResource($bus));
    }
    /**
     * update
     */
    public function update(BusCatalog $busCatalog, UpdateBusCatalogRequest $request){
        $dto = UpdateBusCatalogDTO::fromRequest($request->validated());
        $bus = $this->busCatalogService->update($busCatalog, $dto);
        return successResponse(new BusCatalogResource($bus));
    }
    /**
     * delete
     */
    public function destroy(BusCatalog $busCatalog){
        $busCatalog->delete();
        return successResponse(message: "deleted succssfully");
    }
}
