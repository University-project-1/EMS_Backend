<?php

namespace App\DTOs\SystemUser;

use App\Http\Requests\SystemAuth\RegisterExhibitorRequest;

class RegisterDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ){}

    public static function fromRequest(RegisterExhibitorRequest $request){
        return new self(
            name: $request->validated['name'],
            email: $request->validated['email'],
            password: $request->validated['password'],
        );
    }
}
