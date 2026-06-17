<?php

namespace App\Services\SystemUser\Shared;

use App\DTOs\SystemUser\UpdateProfileDTO;
use App\Models\SystemUser;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function update(SystemUser $user, UpdateProfileDTO $dto){
        $updatedData = $dto->updatePayload();
        $user->update($updatedData);

        if ($dto->avatar instanceof UploadedFile) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($dto->avatar)->toMediaCollection('avatar');
        }

        return $user->refresh();
    }
}
