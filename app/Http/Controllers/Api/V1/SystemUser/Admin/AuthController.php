<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Admin;

use App\DTOs\SystemUser\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Shared\LoginSystemUserRequest;
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
        if(isset($result['error'])){
            return errorResponse(
                message: $result['error'],
                data: null,
                code: $result['statusCode'],
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
}
