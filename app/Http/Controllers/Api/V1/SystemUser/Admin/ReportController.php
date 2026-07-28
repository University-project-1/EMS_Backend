<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Enum\ReportStatus;
use App\Filter\DateFilter;
use App\Filter\ReportSearchFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\ActionReportRequest;
use App\Http\Resources\SystemUser\Admin\ReportResource;
use App\Models\Report;
use App\Services\SystemUser\Admin\ReportService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Admin/Reports')]
class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService){}

    /**
     * statistics
     */
    public function statistics(){
        $statistics = Report::query()
            ->selectRaw('COUNT(*) as total_requests')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_requests', [ReportStatus::PENDING->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as resolved_requests', [ReportStatus::RESOLVED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected_requests', [ReportStatus::REJECTED->value])
            ->firstOrFail();

        return successResponse([
            'total_requests' => (int) $statistics->getAttribute('total_requests'),
            'pending_requests' => (int) $statistics->getAttribute('pending_requests'),
            'resolved_requests' => (int) $statistics->getAttribute('resolved_requests'),
            'rejected_requests' => (int) $statistics->getAttribute('rejected_requests'),
        ]);
    }

    /**
     * all
     */
    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15')]
    #[QueryParameter('filter[search]', type: 'string', description: 'Search reports by event title or booth number.')]
    #[QueryParameter('filter[status]', type: 'string', description: 'Filter by report status (exact match).')]
    #[QueryParameter('filter[created_date]', type: 'string', description: 'Filter by created date (mapped to created_at).')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by created_at. Use -created_at for descending.')]
    #[QueryParameter('include', 'Include related resources (reportable, reporter, resolvedBy)', required: false, type: 'string')]
    public function index(){
        $perPage = min(max(request()->integer('per_page', 15), 1), 100);

        $reports = QueryBuilder::for(Report::class)
            ->allowedFilters(
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_date', new DateFilter(), 'created_at'),
                AllowedFilter::custom('search', new ReportSearchFilter())
            )
            ->allowedIncludes('reportable', 'reporter', 'resolvedBy')
            ->allowedSorts('created_at')
            ->paginate($perPage);

        return successResponse(ReportResource::collection($reports));
    }

    /**
     * resolved
     */
    public function resolved(Report $report, ActionReportRequest $reqeust){
        $this->reportService->resolved($report, $reqeust->validated());

        return successResponse();
    }

    /**
     * rejected
     */
    public function rejected(Report $report, ActionReportRequest $reqeust){
        $this->reportService->rejected($report, $reqeust->validated());

        return successResponse();
    }
}
