<?php

namespace App\DTOs\Mobile;

class VerifyDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $session_id,
        public readonly string $otp,
        public readonly string $phone,
    ){}

    public static function formRequest(array $data): self{
        return new self(
            session_id: $data['session_id'],
            otp: $data['otp'],
            phone: $data['phone']
        );
    }
}
