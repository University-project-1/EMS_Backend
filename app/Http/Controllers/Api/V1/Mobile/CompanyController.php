<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Shared\CompanyResource;
use App\Models\Company;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Visitor/Companies')]
class CompanyController extends Controller
{
    /**
     * all companies
     */
    #[QueryParameter('filter[name]', 'Filter companies partially by name', required: false, type: 'string')]
    #[QueryParameter('filter[business_sector]', 'Filter companies by exact business sector', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort results by field. Available fields: name, created_at. Prefix with - for descending order (e.g., -created_at)', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Default: 15', required: false, type: 'integer')]
    public function index()
    {
        $companies = QueryBuilder::for(Company::class)
            ->allowedFilters('name', AllowedFilter::exact('business_sector'))
            ->allowedSorts('name', 'created_at')
            ->with(['logoMedia', 'booths'])
            ->paginate(request()->query('per_page', 15));

        return successResponse(
            data: CompanyResource::collection($companies),
            message: 'companies retrieved successfully',
        );
    }

    /**
     * show
     */
    public function show(Company $company)
    {
        $company->load(['galleryMedia', 'media','events' => function($query){
            $query->withAvg('reviews', 'rating');
        }, 'logoMedia' , 'booths' => function($query){
            $query->withAvg('reviews', 'rating');
        }, 'booths.hall']);

        return successResponse(
            data: new CompanyResource($company),
            message: 'company retrieved successfully',
        );
    }
}
