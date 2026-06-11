<?php

namespace App\Http\Controllers\Api\V1\Mobile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Auth\ForgotPasswordRequest;
use App\Http\Requests\Mobile\Auth\ResetPasswordRequest;
use App\Http\Requests\Mobile\Auth\VerifyForgotPasswordOtpRequest;
use App\Services\Mobile\AuthService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/ForgotPassword')]
class PasswordController extends Controller
{
    public function __construct(protected AuthService $auth) {}

     /**
      * forgot Password
      */
     public function forgotPassword(ForgotPasswordRequest $request)
    {
        $resetId = $this->auth->forgotPassword($request->validated());
        return successResponse(['reset_id' => $resetId]);
    }

    /**
     * verify Forgot Password Otp
     */
    public function verifyForgotPasswordOtp(VerifyForgotPasswordOtpRequest $request)
    {
        $tempToken = $this->auth->verifyForgotPasswordOtp($request->validated());
        return successResponse(['reset_token' => $tempToken]);
    }

    /**
     * reset Password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->auth->resetPassword($request->validated());
        return successResponse(['message' => 'Password has been reset successfully.']);
    }
}
