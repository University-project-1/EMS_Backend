<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\LoginDTO;
use App\Enum\SystemUserType;
use App\Models\SystemUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function login(LoginDTO $dto)
    {
        $exhibitor = SystemUser::query()->where('email', $dto->email)->where('type', SystemUserType::ADMIN)->first();
        if (! $exhibitor || ! Hash::check($dto->password, $exhibitor->password)) {
            throw new AuthenticationException;
        }
        $token = $exhibitor->createToken('exhibitor_token')->accessToken;

        return ['token' => $token, 'user' => $exhibitor];
    }
}
