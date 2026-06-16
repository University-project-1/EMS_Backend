<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\UpdateProfileDTO;
use App\DTOs\Mobile\VerifyDTO;
use App\Models\User;
use App\Services\Mobile\OtpService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    public function __construct(protected OtpService $otp, protected PhoneService $phoneService) {}

    public function updateProfile(Authenticatable $user, UpdateProfileDTO $dto, string $collectionName = 'user-avatars')
    {
        $updatedData = $dto->updatePayload();

        $user->update($updatedData);

        if ($dto->avatar instanceof UploadedFile) {
            $user->clearMediaCollection($collectionName);
            $user->addMedia($dto->avatar)->toMediaCollection($collectionName);
        }
    }

    public function verifyPhoneUpdate(Authenticatable $user, VerifyDTO $data)
    {
        $phone = $this->phoneService->normalize($data->phone);

        $ifexists = User::where('phone', $phone)->exists();
        
        if ($ifexists) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid phone number.']
            ]);
        }

        $this->otp->verifyOtp($data, 'phone_update');
        $user->update(['phone' => $phone]);
    }
}
