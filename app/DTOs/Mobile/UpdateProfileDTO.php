<?php

namespace App\DTOs\Mobile;

use App\Http\Requests\Mobile\Profile\UpdateProfileRequest;

class UpdateProfileDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $email,
        public readonly ?string $location,
        public readonly ?string $avatar,
        public readonly ?string $job,
    ){}

     public static function fromRequest(UpdateProfileRequest $request): self{
        return new self(
            first_name: $request->validated('first_name'),
            last_name: $request->validated('last_name'),
            email: $request->validated('email'),
            location: $request->validated('location'),
            avatar: $request->validated('avatar'),
            job: $request->validated('job')
        );
    }
}