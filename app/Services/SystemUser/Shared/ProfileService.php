<?php

namespace App\Services\SystemUser\Shared;

use App\DTOs\SystemUser\ProfileUpdateDTO;
use App\Models\SystemUser;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function update(SystemUser $user, ProfileUpdateDTO $dto){
        $updatedData = $dto->updatePayload();
        $user->update($updatedData);

        if ($dto->avatar instanceof UploadedFile) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($dto->avatar)->toMediaCollection('avatar');
        }

        return $user->refresh();
    }
}
