<?php

namespace App\DTOs\Mobile;

use App\Http\Requests\Shared\UpdatePasswordRequest;

class UpdatePasswordDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $current_password,
        public string $new_password,
    ){}

    public static function fromRequest(UpdatePasswordRequest $request): self
    {
        return new self(
            current_password: $request->validated('current_password'),
            new_password: $request->validated('new_password'),
        );
    }
}
