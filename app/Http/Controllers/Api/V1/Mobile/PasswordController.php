<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\DTOs\Mobile\ResetPasswordDTO;
use App\DTOs\Mobile\VerifyDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\ResetPassword\ForgotPasswordRequest;
use App\Http\Requests\Mobile\ResetPassword\ResetPasswordRequest;
use App\Http\Requests\Mobile\VerifyOtpRequest;
use App\Services\Mobile\ResetPasswordService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/ForgotPassword')]
class PasswordController extends Controller
{
    public function __construct(protected ResetPasswordService $password) {}

     /**
      * forgot Password
      */
     public function forgotPassword(ForgotPasswordRequest $request)
    {
        $resetId = $this->password->forgotPassword($request->validated());
        return successResponse(['reset_id' => $resetId]);
    }

    /**
     * verify Forgot Password Otp
     */
    public function verifyForgotPasswordOtp(VerifyOtpRequest $request)
    {
        $tempToken = $this->password->verifyForgotPasswordOtp(VerifyDTO::formRequest($request->validated()));
        return successResponse(['reset_token' => $tempToken]);
    }

    /**
     * reset Password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->password->resetPassword(ResetPasswordDTO::formRequest($request->validated()));
        return successResponse();
    }
}
