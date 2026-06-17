<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\ResetPasswordDTO;
use App\DTOs\Mobile\VerifyDTO;
use App\Models\User;
use Illuminate\Support\Facades\{Cache, Hash};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class ResetPasswordService
{
    public function __construct(protected OtpService $otp, protected PhoneService $phoneService) {}

    public function forgotPassword(array $data)
    {
        $phone = $this->phoneService->normalize($data['phone']);

        $userExists = User::where('phone', $phone)->exists();

        if ($userExists) {
            return $this->otp->generateOtp($phone, "password_reset");
        }

        return Str::uuid()->toString();
    }

    public function verifyForgotPasswordOtp(VerifyDTO $data)
    {
        $phone = $this->phoneService->normalize($data->phone);

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => [__('auth.invalid_credentials')]
            ]);
        }

        $this->otp->verifyOtp($data, "password_reset");

        $tempToken = Str::random(60);

        Cache::put("password_reset_token_{$tempToken}", [
            'phone' => $phone
        ], now()->addMinutes(10));

        return $tempToken;
    }

    public function resetPassword(ResetPasswordDTO $data)
    {   
        $key = "password_reset_token_{$data->reset_token}";
        $tokenDataCache = Cache::get($key);

        if (!$tokenDataCache) {
            throw ValidationException::withMessages([
                'token' => [__('auth.invalid_reset_token')]
            ]);
        }

        $user = User::where('phone', $tokenDataCache['phone'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'token' => [__('auth.invalid_reset_token')]
            ]);
        }

        $user->update([
            'password' => Hash::make($data->password)
        ]);

        $user->tokens()->update([
            'revoked' => true
        ]);

        Cache::forget($key);
    }
}
