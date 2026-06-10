<?php

namespace App\Services\SystemUser\Shared;

use App\DTOs\SystemUser\ProfileUpdateDTO;
use App\Models\SystemUser;

class ProfileService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function update(SystemUser $user, ProfileUpdateDTO $dto){
        $updatedData = $dto->toFilteredArray();
        if (!empty($updatedData)) {
            $user->update($updatedData);
        }
        if ($dto->avatar !== null) {
            $user->addMedia($dto->avatar)->toMediaCollection('avatar');
        }

        return $user->refresh();
    }
}
