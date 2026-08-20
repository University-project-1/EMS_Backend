<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\AnnouncementResource;
use App\Models\Announcement;
use Dedoc\Scramble\Attributes\Group;
use Spatie\QueryBuilder\QueryBuilder;
use Dedoc\Scramble\Attributes\QueryParameter;

#[Group('SystemUser/Exhibitor/Announcement')]
class AnnouncementController extends Controller
{
    #[QueryParameter('sort', 'Sort results. Prefix with - for descending order. Default: -created_at', required: false, type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Use 3 for latest widget.', required: false, type: 'integer')]
    public function index()
    {
        $baseQuery = Announcement::where('is_active', true)
            ->whereIn('receiver', ['Exhibitors', 'all']);

        $announcements = QueryBuilder::for($baseQuery)
            ->defaultSort('-created_at')
            ->allowedSorts('title', 'created_at')
            ->get();  
        return successResponse(data: AnnouncementResource::collection($announcements));
    }
}
