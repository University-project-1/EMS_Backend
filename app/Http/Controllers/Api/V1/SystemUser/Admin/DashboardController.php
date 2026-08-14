<?php
namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Admin\DashboardResource;
use App\Services\SystemUser\Admin\DashboardService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('SystemUser/Admin/Dashboard')]
class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    /**
     * dashboard
     */
    public function index(Request $request): DashboardResource
    {
        return new DashboardResource(
            $this->dashboardService->getDashboard((int) $request->integer('days', 7))
        );
    }
}
