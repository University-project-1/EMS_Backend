<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\DTOs\SystemUser\LoginDTO;
use App\DTOs\SystemUser\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Exhibitor\RegisterExhibitorRequest;
use App\Http\Requests\SystemUser\Shared\LoginSystemUserRequest;
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
            data: ['user'  => $result['user'], 'token' => $result['token']],
            message: 'verify your account',
            code: 200
        );
    }

    /**
     * verify
     * @param mixed $id
     * @param mixed $hash
     */
    public function verify(string $id, string  $hash)
    {
        $user = SystemUser::findOrFail($id);

        if (!$this->authService->verifyEmail($user, $hash)) {
            return errorResponse(
                message: 'Invalid verification link or hash.',
            );
        }
        return successResponse(
            data: $user,
            message: 'Email verified successfully',

        );
    }
    /**
     * login
     */
    public function login(LoginSystemUserRequest $request){
        $dto = LoginDTO::fromRequest($request->validated());
        $result = $this->authService->login($dto);
        if(isset($result['error'])){
            return errorResponse(
                message: $result['error'],
                data: null,
                code: 401,
            );
        }
        return successResponse(
            message: 'login successfully',
            data: ['user' => $result['user'], 'token'=>$result['token']],
        );
    }

    /**
     * logout
     */
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return successResponse(
            data: null,
            message: 'logged out successfully',
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
                message: 'Authenticated successfully.',
                data: [
                    'user' => $result['user'],
                    'access_token' => $result['token']
                ]
            );

        } catch (Exception $e) {
            return errorResponse(
                message: $e->getMessage(),
            );
        }
    }
}
