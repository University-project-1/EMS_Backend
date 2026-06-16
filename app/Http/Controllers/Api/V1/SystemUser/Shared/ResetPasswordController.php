<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Shared;

use App\DTOs\Shared\UpdatePasswordDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\UpdatePasswordRequest;
use App\Http\Requests\SystemUser\Admin\ForgotPasswordRequest;
use App\Http\Requests\SystemUser\Admin\ResetPasswordRequest;
use App\Services\Shared\PasswordService;
use App\Services\SystemUser\Shared\ResetSystemUserPasswordService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Password;

#[Group('SystemUser/reset_password')]
class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly ResetSystemUserPasswordService $resetPasswordService,
        private readonly PasswordService $passwordService,
    ){}

    /**
     * change password
     */
    public function changePassword(UpdatePasswordRequest $request){
        $dto = UpdatePasswordDTO::fromRequest($request->validated());
        $this->passwordService->updatePassword($request->user(), $dto);
        return successResponse(
            data: null,
            message: 'password changed successfully',
            code: 200
        );
    }

    /**
     * send reset link
     */
    public function sendResetLink(ForgotPasswordRequest $request){
        $status = $this->resetPasswordService
            ->sendResetLink($request->validated());

        if ($status !== Password::RESET_LINK_SENT) {
            return errorResponse(
                message: __($status)
            );
        }

        return successResponse(
            message: __($status)
        );
    }

    /**
     * reset password
     */
    public function resetPassword(ResetPasswordRequest $request){
        $status = $this->resetPasswordService->resetPassword($request->validated());

        if ($status !== Password::PASSWORD_RESET) {
            return errorResponse(
                message: __($status)
            );
        }
        return successResponse(
            message: __($status)
        );
    }
}

