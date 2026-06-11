<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;
use App\Trait\HasUpdatePayload;
use Illuminate\Http\UploadedFile;

class ProfileUpdateDTO extends PatchDTO
{
    use HasUpdatePayload;

    public function __construct(
        public readonly ?string $name,
        public readonly ?UploadedFile $avatar,
        array $payload,
    ) {
        parent::__construct($payload);
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            avatar: $data['avatar'] ?? null,
            payload: $data,
        );
    }
}
