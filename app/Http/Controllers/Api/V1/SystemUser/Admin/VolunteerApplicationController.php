<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\ReviewVolunteerApplicationRequest;
use App\Http\Resources\SystemUser\Admin\VolunteerApplicationResource;
use App\Models\SystemUser;
use App\Models\VolunteerApplication;
use App\Services\Shared\VolunteerApplicationService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Group('SystemUser/Admin/VolunteerApplications')]
class VolunteerApplicationController extends Controller
{
    public function __construct(private readonly VolunteerApplicationService $volunteerApplications) {}
    
    /**
     * statistics
     */
    public function statistics(): JsonResponse
    {   
        $statistics = $this->volunteerApplications->statistics();

        return successResponse($statistics);
    }

    /**
     * all 
     */
    #[QueryParameter('filter[status]', 'Filter by application status.', required: false, type: 'string')]
    #[QueryParameter('filter[search]', 'Search by name, email, or phone.', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of records per page, from 1 to 100.', required: false, type: 'integer')]
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        $volunteerApplications = $this->volunteerApplications->paginateForAdministration($perPage);

        return successResponse(VolunteerApplicationResource::collection($volunteerApplications));
    }

    /**
     * show
     */
    public function show(VolunteerApplication $volunteerApplication): JsonResponse
    {
        $volunteerApplication->load(['media', 'reviewer']);

        return successResponse(VolunteerApplicationResource::make($volunteerApplication),);
    }

    /**
     * approve reqeust
     */
    public function approve(ReviewVolunteerApplicationRequest $request, VolunteerApplication $volunteerApplication): JsonResponse
    {
        $reviewer = $request->user('system');

        $this->volunteerApplications->approve($volunteerApplication,$reviewer,$request->validated('review_note'));

        return successResponse();
    }

    /**
     * reject request
     */
    public function reject(ReviewVolunteerApplicationRequest $request, VolunteerApplication $volunteerApplication): JsonResponse
    {
        $reviewer = $request->user('system');

        $this->volunteerApplications->reject($volunteerApplication,$reviewer,$request->validated('review_note'));

        return successResponse();
    }

    public function showCv(VolunteerApplication $volunteerApplication): BinaryFileResponse
    {
        $media = $this->volunteerApplications->cvFor($volunteerApplication);

        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => "inline; filename=\"{$media->file_name}\"",
        ]);
    }
}
