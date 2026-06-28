<?php

namespace App\DTOs\SystemUser;

class RegisterDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $inviteToken,
    ){}

        public static function fromRequest(array $data){
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            inviteToken: $data['invite_token'] ?? null,
        );
    }
}
