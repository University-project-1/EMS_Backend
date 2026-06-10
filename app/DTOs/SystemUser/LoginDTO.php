<?php

namespace App\DTOs\SystemUser;

class LoginDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ){}

    public static function fromRequest(array $data){
        return new self(
            email: $data['email'],
            password: $data['password'],
        );
    }
}
