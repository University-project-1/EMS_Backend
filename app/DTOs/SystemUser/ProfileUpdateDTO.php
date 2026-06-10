<?php

namespace App\DTOs\SystemUser;

use App\Trait\HasFilteredArray;
use Illuminate\Http\UploadedFile;

class ProfileUpdateDTO
{
    use HasFilteredArray;
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?UploadedFile $avatar,
    ){}

    public static function fromRequest(array $data){
        return new self(
            name: $data['name'] ?? null,
            avatar: $data['avatar'] ?? null,
        );
    }
}
