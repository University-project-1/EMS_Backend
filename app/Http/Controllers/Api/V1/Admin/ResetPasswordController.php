<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Shared\ChangePasswordRequest;
use App\Services\Shared\ResetSystemUserPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly ResetSystemUserPasswordService $resetPasswordService,
    ){}

    public function changePassword(ChangePasswordRequest $request){
        $this->resetPasswordService->changePassword($request->user(), $request->validated(['new_password']));
        return successResponse(
            data: null,
            message: 'password changed successfully',
            code: 200
        );
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $status = $this->resetPasswordService->sendResetLink($request->validated());

        if ($status === Password::RESET_LINK_SENT) {
            return successResponse(
                data: null,
                message:  __($status),
            );
        }

        return errorResponse(
            message: __($status),
        );
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->resetPasswordService->resetPassword($request->validated());

        if (!$status === Password::PASSWORD_RESET) {
            return errorResponse(
                data: null,
                message: __($status),
            );
        }

        return successResponse(
            data: null,
            message: __($status),
        );
    }
}

