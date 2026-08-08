<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\NotificationResource;
use App\Services\Shared\NotificationService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Notifications\DatabaseNotification;

#[Group('Notifications')]
class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService,) {}

    /**
     * statistics
     */
    public function statistics(string $guardName)
    {
        $statistics = $this->notificationService->statistics($guardName);

        return successResponse($statistics);
    }

    /**
     * all notifications
     */
    #[QueryParameter('filter[type]',type: 'string',description: 'Filter notifications by exact type.',required: false)]
    #[QueryParameter('per_page',type: 'integer',description: 'Number of notifications per page. Default: 15, Max: 100.',required: false)]
    #[QueryParameter('sort',type: 'string',description: 'Sort by created_at. Use -created_at for descending.',required: false)]
    public function index(string $guardName)
    {
        $perPage = min(max(request()->integer('per_page', 10), 1),100);

        $notifications = $this->notificationService->index($guardName, $perPage);

        return successResponse(NotificationResource::collection($notifications));
    }

    /**
     * mark all notifications as read
     */
    public function markAllAsRead(string $guardName)
    {
        $this->notificationService->markAllAsRead($guardName);

        return successResponse();
    }

    /**
     * mark notification as read
     */
    public function markAsRead(DatabaseNotification $notification,string $guardName) {
        $this->notificationService->markAsRead($notification,$guardName);

        return successResponse();
    }

    /**
     * delete notification
     */
    public function destroy(DatabaseNotification $notification,string $guardName) {
        $this->notificationService->destroy($notification,$guardName);

        return successResponse();
    }
}