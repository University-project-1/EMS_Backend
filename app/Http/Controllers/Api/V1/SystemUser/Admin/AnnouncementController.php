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
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcementService
    ){}

    /**
     * all
     */
    public function index(){
        $announcements = QueryBuilder::for(Announcement::class)
            ->allowedFilters(
                'title', 'description',
                AllowedFilter::exact('receiver', 'is_active')
            )
            ->allowedSorts('title', 'created_at')
            ->paginate(request()->query('per_page', 10));
        return successResponse(data: AnnouncementResource::collection($announcements));
    }
    /**
     * show
     */
    public function show(Announcement $announcement){
        return successResponse(data: new AnnouncementResource($announcement));
    }
    /**
     * create
     */
    public function store(StoreAnnouncementRequest $request){
        $dto = AnnouncementDTO::fromRequest($request->validated());
        $announcement = $this->announcementService->create($dto);
        return successResponse(data: new AnnouncementResource($announcement));
    }
    /**
     * Update
     */
    public function update(Announcement $announcement, UpdateAnnouncementRequest $request){
        $dto = UpdateAnnouncementDTO::fromRequest($request->validated());
        $updatedAnnouncement = $this->announcementService->edit($announcement, $dto);
        return successResponse(data: new AnnouncementResource($updatedAnnouncement));
    }
    /**
     * delete
     */
    public function delete(Announcement $announcement){
        $announcement->delete();
        return successResponse(data: null, message: "announcement Deleted Successfully");
    }
}
