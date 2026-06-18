<?php

namespace App\DTOs\Mobile;

class ResetPasswordDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $password,
        public readonly string $reset_token
    ){}

    public static function formRequest(array $data): self
    {
        return new self(
            password: $data['password'],
            reset_token: $data['reset_token']
        );
    }
}
