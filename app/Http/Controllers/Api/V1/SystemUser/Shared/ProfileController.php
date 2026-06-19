<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Shared;


use App\DTOs\SystemUser\UpdateProfileDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Shared\UpdateProfileRequest;
use App\Http\Resources\SystemUser\Shared\ProfileResource;
use App\Services\SystemUser\Shared\ProfileService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('SystemUser/Profile')]
class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    /**
     * show
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('media');

        return successResponse(
            data: new ProfileResource($user),
            message: __('profile.show_success'),
        );
    }

    /**
     * update
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $dto = UpdateProfileDTO::fromRequest($request->validated());

        $updatedUser = $this->profileService->update($user, $dto);

        $updatedUser->load('media');

        return successResponse(
            data: new ProfileResource($updatedUser),
            message: __('profile.update_success')
        );
    }
}
