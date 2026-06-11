<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\UpdateProfileDTO;
use App\Services\Mobile\OtpService;
use Illuminate\Contracts\Auth\Authenticatable;

class ProfileService
{
    public function __construct(protected OtpService $otp) {}

    public function updateProfile(Authenticatable $user, UpdateProfileDTO $dto, string $collectionName = 'user-avatars')
    {
        $updatedData = $dto->updatePayload();

        $user->update($updatedData);

        if (isset($dto->avatar)) {
            $user->clearMediaCollection($collectionName);
            $user->addMedia($dto->avatar)->toMediaCollection($collectionName);
        }
    }

    public function verifyPhoneUpdate(Authenticatable $user, array $data)
    {
        $this->otp->verifyOtp($data, 'phone_update');
        $user->update(['phone' => $data['phone']]);
    }
}
