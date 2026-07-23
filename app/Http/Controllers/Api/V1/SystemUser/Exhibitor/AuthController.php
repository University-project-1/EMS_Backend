<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\DTOs\SystemUser\LoginDTO;
use App\DTOs\SystemUser\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Exhibitor\RegisterExhibitorRequest;
use App\Http\Requests\SystemUser\Shared\LoginSystemUserRequest;
use App\Http\Resources\SystemUser\Shared\ProfileResource;
use App\Models\SystemUser;
use App\Services\SystemUser\Exhibitor\AuthService;
use App\Services\SystemUser\Exhibitor\GoogleAuthService;
use Dedoc\Scramble\Attributes\Group;
use Exception;
use Illuminate\Http\Request;
#[Group('SystemUser/Exhibitor/Auth')]
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly GoogleAuthService $googleAuthService,
    ){}

    /**
     * register
     */
    public function register(RegisterExhibitorRequest $request){
        $dto = RegisterDTO::fromRequest($request->validated());
        $result = $this->authService->register($dto);
        return successResponse(
            data: ['user'  => new ProfileResource($result['user']), 'token' => $result['token']],
            message: __($result['message']),
        );
    }

    /**
     * verify
     * @param mixed $id
     * @param mixed $hash
     */
    public function verify(string $id, string $hash)
    {
        $user = SystemUser::findOrFail($id);

        $this->authService->verifyEmail($user, $hash);

        return successResponse(
            data: $user,
            message: __('auth.email_verified')
        );
    }

    /**
     * resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        $this->authService->resendVerificationEmail($request->user('system')) ;

        return successResponse(
            message: __('auth.verification_sent')
        );
    }
    /**
     * login
     */
    public function login(LoginSystemUserRequest $request){
        $dto = LoginDTO::fromRequest($request->validated());
        $result = $this->authService->login($dto);

        return successResponse(
            message: __('auth.login_success'),
            data: ['user' => new ProfileResource($result['user']), 'token'=>$result['token']],
        );
    }

    /**
     * logout
     */
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return successResponse(
            data: null,
            message: __('auth.logout_success'),
        );
    }

    /**
     * googleAuth
     */
    public function googleAuth(Request $request)
    {
        $request->validate([
            'token' => 'required|string', // The Access Token from Frontend Google SDK
        ]);

        try {
            $result = $this->googleAuthService->handleGoogleProviderToken($request->token);

            return successResponse(
                message: __('auth.google_auth_success'),
                data: [
                    'user' => new ProfileResource($result['user']),
                    'access_token' => $result['token']
                ]
            );

        } catch (Exception $e) {
            return errorResponse(
                message: $e->getMessage(),
            );
        }
    }

    /**
     * check account verification
     */
    public function checkStatus(Request $request)
    {
        return successResponse(
            message: __('auth.status_fetched'),
            data: [
                'is_verified' => $request->user('system')->hasVerifiedEmail(),
            ]
        );
    }
}
