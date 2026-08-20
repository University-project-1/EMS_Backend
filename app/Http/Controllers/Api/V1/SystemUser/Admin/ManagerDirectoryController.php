<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Enum\Status;
use App\Enum\SystemUserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Admin\ManagerResource;
use App\Models\Booth;
use App\Models\Company;
use App\Models\SystemUser;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/ManagerDirectory')]
class ManagerDirectoryController extends Controller
{
    public function directory()
    {
        $stats = Cache::remember('admin_directory_stats', 600, function () {
            return [
                'total_companies' => Company::where('status', Status::APPROVED->value)->count(),
                'total_booths' => Booth::count(),
                'total_managers' => SystemUser::where('type', SystemUserType::EXHIBITOR->value)->count(),
            ];
        });

        return successResponse(
            data: $stats,
            message: 'statistics retrieved successfully',
        );
    }

    /**
     * all managers
     */
    #[QueryParameter('filter[name]', 'Filter managers partially by name', required: false, type: 'string')]
    #[QueryParameter('filter[email]', 'Filter managers partially by email', required: false, type: 'string')]
    #[QueryParameter('filter[phone]', 'Filter managers partially by phone', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort results by field (name, created_at). Prefix with - for descending order', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Default: 15', required: false, type: 'integer')]
    public function index()
    {
        Log::info(1);
        Gate::authorize('viewAny', SystemUser::class);
        Log::info(2);

        $managers = QueryBuilder::for(SystemUser::class)
            ->where('type', SystemUserType::EXHIBITOR->value)
            ->allowedFilters('name', 'email', 'phone')
            ->allowedSorts('name', 'created_at')
            ->with('media')
            ->withCount('companies')
            ->defaultSort('-created_at')
            ->paginate(request()->query('per_page', 15));

        $managers->getCollection()->each(function (SystemUser $manager): void {
            $manager->setAttribute('booths_count', Booth::query()->accessibleBy($manager)->count());
        });
        return successResponse(
            data: ManagerResource::collection($managers),
            message: 'managers retrieved successfully',
        );
    }

    public function show(SystemUser $manager)
    {
        Gate::authorize('view', $manager);
        $manager->loadMissing(['media', 'companies.logoMedia', 'companies.booths.hall']);

        return successResponse(
            data: new ManagerResource($manager),
            message: 'manager retrieved successfully',
        );
    }
}
