<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\DTOs\Mobile\ReportDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\StoreReportRequest;
use App\Services\Mobile\ReportService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/Report')]
class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService){}

    /**
     * store
     */
    public function store(StoreReportRequest $request){
        $this->reportService->store(ReportDTO::fromRequest($request->validated()), auth('mobile')->user());

        return successResponse();
    }
}
