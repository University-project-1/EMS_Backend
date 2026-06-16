<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\StoreFCMRequest;
use App\Services\Shared\FCMService;
use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function __construct(private FCMService $fcm) {}

    public function store(StoreFCMRequest $request, string $guardName)
    {
        $this->fcm->store($request->validated(), $guardName);

        return successResponse();
    }
}
