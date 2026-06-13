<?php

namespace App\DTOs\Mobile;

use App\DTOs\PatchDTO;
use App\Trait\HasUpdatePayload;
use Illuminate\Http\UploadedFile;

class UpdateProfileDTO extends PatchDTO
{
    use HasUpdatePayload;
    public function __construct(
        public readonly ?string $firstName,
        public readonly ?string $last_name,
        public readonly ?string $email,
        public readonly ?string $location,
        public readonly ?UploadedFile $avatar,
        public readonly ?string $job,
        protected array $payload,
    ) {
        parent::__construct($payload);
    }

     public static function fromRequest(array $data): self{
        return new self(
            firstName: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            email: $data['email'] ?? null,
            location: $data['location'] ?? null,
            avatar: $data['avatar'] ?? null,
            job: $data['job'] ?? null,
            payload: $data ?? null,
        );
    }
}
