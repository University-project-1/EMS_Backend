<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Auth\ForgotPasswordRequest;
use App\Http\Requests\Mobile\Auth\LoginRequest;
use App\Http\Requests\Mobile\Auth\RegisterRequest;
use App\Http\Requests\Mobile\Auth\ResendOtpRequest;
use App\Http\Requests\Mobile\Auth\ResetPasswordRequest;
use App\Http\Requests\Mobile\Auth\VerifyForgotPasswordOtpRequest;
use App\Http\Requests\Mobile\Auth\VerifyRegisterRequest;
use App\Http\Resources\Mobile\UserResource;
use App\Services\Mobile\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $auth) {}
    public function register(RegisterRequest $request)
    {
        $registrationId = $this->auth->register($request->validated());
        return successResponse(['registration_id' => $registrationId]);
    }

    public function verifyRegister(VerifyRegisterRequest $request)
    {
        $data = $this->auth->verifyRegister($request->validated());

        return successResponse([
            'user' => UserResource::make($data['user']),
            'token' => $data['token']
        ]);
    }
    public function login(LoginRequest $request)
    {
        $data = $this->auth->login($request->validated());

        return successResponse([
            'user' => UserResource::make($data['user']),
            'token' => $data['token']
        ]);
    }

    public function logout()
    {
        $this->auth->logout();

        return successResponse();
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $resetId = $this->auth->forgotPassword($request->validated());
        return successResponse(['reset_id' => $resetId]);
    }

    public function verifyForgotPasswordOtp(VerifyForgotPasswordOtpRequest $request)
    {
        $tempToken = $this->auth->verifyForgotPasswordOtp($request->validated());
        return successResponse(['reset_token' => $tempToken]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->auth->resetPassword($request->validated());
        return successResponse(['message' => 'Password has been reset successfully.']);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $session_id = $this->auth->resendOtp($request->validated());
        return successResponse(['session_id' => $session_id]);
    }
}
