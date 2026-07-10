<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Admin\CompanyDirectoryResource;
use App\Models\Company;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/CompanyDirectory')]
class CompanyDirectoryController extends Controller
{
    /**
     * all companies
     */
    #[QueryParameter('filter[name]', 'Filter companies partially by name', required: false, type: 'string')]
    #[QueryParameter('filter[business_sector]', 'Filter companies by exact business sector', required: false, type: 'string')]
    #[QueryParameter('filter[status]', 'Filter companies by exact status', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort results by field (name, created_at). Prefix with - for descending order', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Default: 15', required: false, type: 'integer')]
    public function index()
    {
        Gate::authorize('viewAny', Company::class);

        $companies = QueryBuilder::for(Company::class)
            ->allowedFilters('name', AllowedFilter::exact('business_sector'), AllowedFilter::exact('status'))
            ->allowedSorts('name', 'created_at')
            ->with('logoMedia')
            ->withCount(['systemUsers', 'booths'])
            ->defaultSort('-created_at')
            ->paginate(request()->query('per_page', 15));

        return successResponse(
            data: CompanyDirectoryResource::collection($companies),
            message: 'companies retrieved successfully',
        );
    }

    public function show(Company $company)
    {
        Gate::authorize('view', $company);

        $company->loadMissing(['systemUsers.media', 'booths.hall']);

        return successResponse(
            data: new CompanyDirectoryResource($company),
            message: 'company retrieved successfully',
        );
    }
}
