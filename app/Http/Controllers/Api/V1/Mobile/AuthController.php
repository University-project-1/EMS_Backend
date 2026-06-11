<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Auth\LoginRequest;
use App\Http\Requests\Mobile\Auth\RegisterRequest;
use App\Http\Requests\Mobile\Auth\ResendOtpRequest;
use App\Http\Requests\Mobile\Auth\VerifyRegisterRequest;
use App\Http\Resources\Mobile\UserResource;
use App\Services\Mobile\AuthService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/Auth')]
class AuthController extends Controller
{
    public function __construct(protected AuthService $auth) {}
    /**
     * register
     */
    public function register(RegisterRequest $request)
    {
        $registrationId = $this->auth->register($request->validated());
        return successResponse(['registration_id' => $registrationId]);
    }

    /**
     * verify register
     */
    public function verifyRegister(VerifyRegisterRequest $request)
    {
        $data = $this->auth->verifyRegister($request->validated());

        return successResponse([
            'user' => UserResource::make($data['user']),
            'token' => $data['token']
        ]);
    }
    /**
     * login
     */
    public function login(LoginRequest $request)
    {
        $data = $this->auth->login($request->validated());

        return successResponse([
            'user' => UserResource::make($data['user']),
            'token' => $data['token']
        ]);
    }

    /**
     * logout
     */
    public function logout()
    {
        $this->auth->logout();

        return successResponse();
    }

    /**
     * resend otp
     */
    public function resendOtp(ResendOtpRequest $request)
    {
        $session_id = $this->auth->resendOtp($request->validated());
        return successResponse(['session_id' => $session_id]);
    }
}
