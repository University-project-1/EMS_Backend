<?php

namespace App\Http\Controllers\SystemUser\Shared;

use App\DTOs\SystemUser\ProfileUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\Shared\UpdateProfileRequest;
use App\Http\Resources\SystemUser\Shared\ProfileResource;
use App\Services\SystemUser\Shared\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    public function show(Request $request)
    {
        $user = $request->user()->load('media');

        return successResponse(
            data: new ProfileResource($user),
            message: 'Profile retrieved successfully.',
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $dto = ProfileUpdateDTO::fromRequest($request->validated());

        $updatedUser = $this->profileService->update($user, $dto);

        $updatedUser->load('media');

        return successResponse(
            data: new ProfileResource($updatedUser),
            message: __('Profile updated successfully.')
        );
    }
}
