<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Shared\LoginSystemUserRequest;
use App\Http\Resources\SystemUser\Shared\ProfileResource;
use App\Services\SystemUser\Admin\AuthService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('SystemUser/Admin/Auth')]
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ){}
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
}
