<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\VerifyDTO;
use App\Jobs\SendOtpWhatsappJob;
use App\Models\OtpCode;
use Illuminate\Support\Facades\{Cache, DB, Hash};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class OtpService
{
    public function __construct(protected PhoneService $phoneService) {}

    const MAX_ATTEMPTS = 3;
    const COOLDOWN_SECONDS = 60;
    const DAILY_LIMIT = 10;
    public function generateOtp(string $phone, string $type)
    {
        // Redis Lock
        return Cache::lock("otp_lock_{$phone}_{$type}", 5)->block(3, function () use ($phone, $type) {

            $this->checkRateLimits($phone, $type);

            $otp = (string) random_int(100000, 999999);
            $registrationId = (string) Str::uuid();

            DB::transaction(function () use ($phone, $type, $otp, $registrationId) {

                OtpCode::where('phone', $phone)
                    ->where('type', $type)
                    ->where('is_used', false)
                    ->lockForUpdate()
                    ->update(['is_used' => true,]);

                OtpCode::create([
                    'phone' => $phone,
                    'type' => $type,
                    'otp' => Hash::make($otp),
                    'session_id' => $registrationId,
                    'expires_at' => now()->addMinutes(5),
                ]);

                // dispatch job send otp by whatsapp
                dispatch(new SendOtpWhatsappJob($phone, $otp))->afterCommit();
            });
            return $registrationId;
        });
    }

    public function verifyOtp(VerifyDTO $data, string $type)
    {
            $phone = $this->phoneService->normalize($data->phone);

            $otpRecord = OtpCode::query()
                ->lockForUpdate()
                ->where('phone', $phone)
                ->where('session_id', $data->session_id)
                ->where('type', $type)
                ->where('is_used', false)
                ->first();

            if (!$otpRecord || $otpRecord->expires_at < now())
                throw ValidationException::withMessages(['otp' => ['Invalid or expired session.']]);

            if ($otpRecord->attempts >= self::MAX_ATTEMPTS) {
                $otpRecord->update(['is_used' => true]);
                throw ValidationException::withMessages(['otp' => ['Too many failed attempts. Session blocked.']]);
            }
            if (!Hash::check($data->otp, $otpRecord->otp)) {
                $otpRecord->increment('attempts');
                throw ValidationException::withMessages(['otp' => ['The OTP code is incorrect']]);
            }
            $otpRecord->update(['is_used' => true]);
    }

    private function checkRateLimits(string $phone, string $type)
    {
        // Cooldown (60s)
        $lastOtp = OtpCode::where('phone', $phone)
            ->where('type', $type)
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at > now()->subSeconds(self::COOLDOWN_SECONDS)) {
            throw ValidationException::withMessages([
                'otp' => ['Please wait before requesting another OTP']
            ]);
        }

        // Daily limit (10)
        $count = OtpCode::where('phone', $phone)
            ->where('type', $type)
            ->whereDate('created_at', today())
            ->count();

        if ($count >= self::DAILY_LIMIT) {
            throw ValidationException::withMessages([
                'otp' => ['Daily OTP limit exceeded']
            ]);
        }
    }
    
}
