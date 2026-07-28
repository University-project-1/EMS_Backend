<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\AnnouncementDTO;
use App\DTOs\SystemUser\UpdateAnnouncementDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Admin\StoreAnnouncementRequest;
use App\Http\Requests\SystemUser\Admin\UpdateAnnouncementRequest;
use App\Http\Resources\Shared\AnnouncementResource;
use App\Models\Announcement;
use App\Services\SystemUser\Admin\AnnouncementService;
use Dedoc\Scramble\Attributes\Group;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Dedoc\Scramble\Attributes\QueryParameter;

#[Group('SystemUser/Admin/Announcement')]
class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcementService
    ){}

    #[QueryParameter('filter[title]', 'Filter announcements by partial title', required: false, type: 'string')]
    #[QueryParameter('filter[receiver]', 'Filter by receiver (Exhibitors, visitors, all)', required: false, type: 'string')]
    #[QueryParameter('filter[is_active]', 'Filter by active status', required: false, type: 'boolean')]
    #[QueryParameter('sort', 'Sort results (title, created_at). Prefix with - for descending order', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page', required: false, type: 'integer')]
    /**
     * all
     */
    public function index()
    {
        $announcements = QueryBuilder::for(Announcement::class)
            ->allowedFilters(
                'title',
                AllowedFilter::exact('receiver'),
                AllowedFilter::exact('is_active')
            )
            ->allowedSorts('title', 'created_at')
            ->defaultSort('-created_at')
            ->paginate(request()->query('per_page', 10));
        return successResponse(data: AnnouncementResource::collection($announcements));
    }
    /**
     * show
     */
    public function show(Announcement $announcement)
    {
        return successResponse(data: new AnnouncementResource($announcement));
    }
    /**
     * store
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $dto = AnnouncementDTO::fromRequest($request->validated());
        $announcement = $this->announcementService->create($dto);
        return successResponse(data: new AnnouncementResource($announcement));
    }
    /**
     * update
     */
    public function update(Announcement $announcement, UpdateAnnouncementRequest $request)
    {
        $dto = UpdateAnnouncementDTO::fromRequest($request->validated());
        $updatedAnnouncement = $this->announcementService->edit($announcement, $dto);
        return successResponse(data: new AnnouncementResource($updatedAnnouncement));
    }
    /**
     * delete
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return successResponse(data: null, message: "Announcement Deleted Successfully");
    }
}
