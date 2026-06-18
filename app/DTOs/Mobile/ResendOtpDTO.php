<?php

namespace App\DTOs\Mobile;

class ResendOtpDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $phone,
        public readonly string $session_id, 
    ){}

    public static function formRequest(array $data): self
    {
        return new self(
            phone: $data['phone'],
            session_id: $data['session_id']
        );
    }
}
