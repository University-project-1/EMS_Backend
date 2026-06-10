<?php

namespace App\DTOs\SystemUser;

use App\Http\Requests\System\Shared\UpdateProfileRequest;
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

    public static function fromRequest(UpdateProfileRequest $request){
        return new self(
            name: $request->validated('name') ?? null,
            avatar: $request->validated('avatar') ?? null,
        );
    }
}
