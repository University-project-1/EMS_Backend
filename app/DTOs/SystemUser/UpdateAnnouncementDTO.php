<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;
use App\Trait\HasUpdatePayload;
use Illuminate\Http\UploadedFile;

class UpdateAnnouncementDTO extends PatchDTO
{
    use HasUpdatePayload;
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?string $receiver,
        public readonly ?bool $isActive,
        public readonly ?UploadedFile $media,
        array $payload
    ){
        parent::__construct($payload);
    }

    public static function fromRequest(array $data){
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            receiver: $data['receiver'] ?? null,
            isActive: $data['is_active'] ?? null,
            media: $data['media'] ?? null,
            payload: $data
        );
    }
}
