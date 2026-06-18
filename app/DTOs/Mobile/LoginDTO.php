<?php

namespace App\DTOs\Mobile;

class LoginDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $phone,
        public string $password
    ){}

    public static function formRequest(array $data): self
    {
        return new self(
            phone: $data['phone'],
            password: $data['password']
        );
    } 
}
