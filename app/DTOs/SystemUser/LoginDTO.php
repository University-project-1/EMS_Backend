<?php

namespace App\DTOs\SystemUser;

use App\Http\Requests\SystemAuth\LoginSystemUserRequest;

class LoginDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ){}

    public static function fromRequest(LoginSystemUserRequest $request){
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }
}
