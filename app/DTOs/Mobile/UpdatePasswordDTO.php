<?php

namespace App\DTOs\Mobile;


class UpdatePasswordDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $current_password,
        public readonly string $new_password,
    ){}

    public static function fromRequest(array $data): self
    {
        return new self(
            current_password: $data['current_password'],
            new_password: $data['new_password'],
        );
    }
}
