<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\DTOs\Mobile\UpdateProfileDTO;
use App\DTOs\Shared\UpdatePasswordDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Profile\UpdatePhoneRequest;
use App\Http\Requests\Mobile\Profile\UpdateProfileRequest;
use App\Http\Requests\Mobile\Profile\VerifyPhoneRequest;
use App\Http\Requests\Shared\UpdatePasswordRequest;
use App\Http\Resources\Mobile\UserResource;
use App\Services\Mobile\OtpService;
use App\Services\Mobile\ProfileService;
use App\Services\Shared\PasswordService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/Profile')]
class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profile,
        protected PasswordService $password,
        protected OtpService $otp
    ) {}

    /**
     * show
     */
    public function show()
    {
        return successResponse(UserResource::make(auth('mobile')->user()));
    }

    /**
     * updateProfile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $this->profile->updateProfile(auth('mobile')->user(), UpdateProfileDTO::fromRequest($request->validated()), 'user-avatars');

        return successResponse(UserResource::make(auth('mobile')->user()->refresh()));
    }


    /**
     * updatePassword
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->password->updatePassword($request->user(), UpdatePasswordDTO::fromRequest($request->validated()));

        return successResponse();
    }

    /**
     * requestPhoneUpdate
     */
    public function requestPhoneUpdate(UpdatePhoneRequest $request)
    {
        $registrationId = $this->otp->generateOtp($request->only('phone'), 'phone_update');

        return successResponse(['registration_id' => $registrationId]);
    }

    /**
     * verifyPhoneUpdate
     */
    public function verifyPhoneUpdate(VerifyPhoneRequest $request)
    {
        $this->profile->verifyPhoneUpdate(auth('mobile')->user(), $request->validated());

        return successResponse(UserResource::make(auth('mobile')->user()->refresh()));
    }
}
