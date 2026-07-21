<?php

namespace App\DTOs\SystemUser;

use App\Trait\HasSnakeCaseArray;
use Illuminate\Http\UploadedFile;

class AnnouncementDTO
{
    use HasSnakeCaseArray;
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $receiver,
        public readonly ?bool $isActive,
        public readonly ?UploadedFile $media
    ){}

    public static function fromRequest(array $data){
        return new self(
            title: $data['title'],
            description: $data['description'],
            receiver: $data['receiver'],
            isActive: $data['is_active'] ?? true,
            media: $data['media'] ?? null
        );
    }
}
