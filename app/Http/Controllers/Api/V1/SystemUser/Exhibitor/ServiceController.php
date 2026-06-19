<?php

namespace App\Http\Controllers\Api\v1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Shared\ServiceResource;
use App\Models\Service;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Exhibitor/Services')]
class ServiceController extends Controller
{
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
            ->where('is_active', true)
            ->paginate(request()->query('per_page', 15));

        return successResponse(
            data: ServiceResource::collection($services),
            message: 'Services returned successfully',
        );
    }
}
