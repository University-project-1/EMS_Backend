<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\UpdateProfileDTO;
use App\Services\Mobile\OtpService;
use Illuminate\Contracts\Auth\Authenticatable;

class ProfileService
{
    public function __construct(protected OtpService $otp) {}

     public function updateProfile(Authenticatable $user, UpdateProfileDTO $data, string $collectionName = 'user-avatars')
    {
            $user->update([
                'first_name' => $data->first_name ?? $user->first_name,
                'last_name' => $data->last_name ?? $user->last_name,
                'email' => $data->email ?? $user->email,
                'job' => $data->job ?? $user->job,
                'location' => $data->location ?? $user->location,
            ]);

        if (isset($data->avatar)) {
            $user->clearMediaCollection($collectionName);
            $user->addMedia($data->avatar)->toMediaCollection($collectionName);
        }
    }

    public function verifyPhoneUpdate(Authenticatable $user, array $data)
    {
        $this->otp->verifyOtp($data, 'phone_update');
        $user->update(['phone' => $data['phone']]);
    }
}
