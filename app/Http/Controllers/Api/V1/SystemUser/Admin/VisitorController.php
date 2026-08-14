<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Filter\CreatedFromFilter;
use App\Filter\CreatedToFilter;
use App\Filter\VisitorSearchFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Admin\VisitorResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/Visitors')]
class VisitorController extends Controller
{
    /**
     * statistics
     */
    public function statistics()
    {
        $statistics = User::query()
            ->selectRaw('COUNT(*) as total_visitors')
            ->selectRaw('SUM(CASE WHEN gender = ? THEN 1 ELSE 0 END) as male_visitors',['male'])
            ->selectRaw('SUM(CASE WHEN gender = ? THEN 1 ELSE 0 END) as female_visitors',['female'])
            ->firstOrFail();

        return successResponse([
            'total_visitors' => (int) $statistics->getAttribute('total_visitors'),
            'male_visitors' => (int) $statistics->getAttribute('male_visitors'),
            'female_visitors' => (int) $statistics->getAttribute('female_visitors'),
        ]);
    }

    /**
     * all visitors
     */
    #[QueryParameter('filter[search]', type: 'string', description: 'Search visitors by first name, last name, email, or phone.')]
    #[QueryParameter('filter[gender]', type: 'string', description: 'Filter visitors by gender (exact match).')]
    #[QueryParameter('filter[job]', type: 'string', description: 'Filter visitors by job title (partial match).')]
    #[QueryParameter('filter[location]', type: 'string', description: 'Filter visitors by location (partial match).')]
    #[QueryParameter('filter[created_from]', type: 'string', description: 'Filter visitors created on or after this date (YYYY-MM-DD).')]
    #[QueryParameter('filter[created_to]', type: 'string', description: 'Filter visitors created on or before this date (YYYY-MM-DD).')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by first_name, created_at, or birthday. Prefix with "-" for descending order.')]
    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15, maximum: 100.')]
    public function index(){
        $perPage = min(max(request()->integer('per_page', 15), 1), 100);

        $visitors = QueryBuilder::for(User::class)
            ->allowedFilters(
                AllowedFilter::custom('search', new VisitorSearchFilter()),
                AllowedFilter::exact('gender'),
                AllowedFilter::partial('job'),
                AllowedFilter::partial('location'),
                AllowedFilter::custom('created_from', new CreatedFromFilter(), 'created_at'),
                AllowedFilter::custom('created_to', new CreatedToFilter(), 'created_at'),
            )
            ->allowedSorts(
                'first_name',
                'created_at',
                'birthday',
            )
            ->defaultSort('-created_at')
            ->with('media')
            ->paginate($perPage);

        return successResponse(
            VisitorResource::collection($visitors)
        );
    }
}
