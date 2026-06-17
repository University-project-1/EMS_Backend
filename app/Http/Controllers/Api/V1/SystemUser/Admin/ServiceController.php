<?php

namespace App\Http\Controllers\Api\v1\SystemUser\Admin;

use App\DTOs\SystemUser\ServiceDTO;
use App\DTOs\SystemUser\UpdateServiceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\StoreServiceRequest;
use App\Http\Requests\SystemUser\Admin\UpdateServiceRequest;
use App\Http\Resources\SystemUser\Shared\ServiceResource;
use App\Models\Service;
use App\Services\SystemUser\Admin\ServiceService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/Services')]
class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $service,
    ){}

    #[QueryParameter('filter[name]', type: 'string', description: 'Filter services partially by name. Example: Screen')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by field (price, name). Prefix with "-" for descending. Example: -price')]
    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15')]
    /**
     * all services
     */
    public function index()
    {

        $services = QueryBuilder::for(Service::class)
            ->allowedFilters('name')
            ->allowedSorts('price', 'name')
            ->paginate(request()->query('per_page', 15));

        return successResponse(
            data: ServiceResource::collection($services),
            message: 'Services returned successfully',
        );
    }
    /**
     * show
     * @param Service $service
     */
    public function show(Service $service){
        return successResponse(
            data: new ServiceResource($service),
            message: 'service returned successfully',
        );
    }

    /**
     * update
     * @param Service $service
     */
    public function update(Service $service, UpdateServiceRequest $request){
        $dto = UpdateServiceDTO::fromRequest($request->validated());
        $updatedService = $this->service->update($service, $dto);
        return successResponse(
            data: new ServiceResource($updatedService),
            message: 'service updated successfully',
        );
    }

    /**
     * store
     */
    public function store(StoreServiceRequest $request){
        $dto = ServiceDTO::fromRequest($request->validated());
        $service = $this->service->create($dto);
        return successResponse(
            data: new ServiceResource($service),
            message: 'service created successfully',
        );
    }
}
