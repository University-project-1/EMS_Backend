<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\LoginDTO;
use App\DTOs\Mobile\RegisterDTO;
use App\DTOs\Mobile\ResendOtpDTO;
use App\DTOs\Mobile\VerifyDTO;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\{Cache, DB, Hash};
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected OtpService $otp, protected PhoneService $phoneService) {}

    public function register(RegisterDTO $data)
    {
        $phone = $this->phoneService->normalize($data->phone);

        $user = User::where('phone', $phone)->orWhere('email', $data->email)->first();
        if ($user) {
            throw ValidationException::withMessages([
                'phone' => [__('auth.invalid_registration_state')]
            ]);
        }

        $registrationId = $this->otp->generateOtp($phone, "registration");

        Cache::put("reg_data_{$registrationId}", [
            'phone' => $phone,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'job' => $data->job,
            'location' => $data->location,
            'gender' => $data->gender->value,
            'birthday' => $data->birthday->toDateString(),  
            'password' => $data->password
        ], now()->addMinutes(10));

        return $registrationId;
    }

    public function verifyRegister(VerifyDTO $data)
    {
        return DB::transaction(function () use ($data) {
            $this->otp->verifyOtp($data, "registration");

            $key = "reg_data_{$data->session_id}";

            $userDataCache = Cache::get($key);

            if (!$userDataCache)
                throw ValidationException::withMessages([
                    'user' => [__('auth.registration_session_expired')]
                ]);

            $exists = User::where('phone', $userDataCache['phone'])
                ->exists();

            if ($exists){
                throw ValidationException::withMessages([
                    'phone' => [__('auth.invalid_registration_state')]
                ]);
            }

            $user = User::create([
                    'phone' => $userDataCache['phone'],
                    'first_name' => $userDataCache['first_name'],
                    'last_name' => $userDataCache['last_name'],
                    'email' => $userDataCache['email'],
                    'job' => $userDataCache['job'],
                    'location' => $userDataCache['location'],
                    'gender' => $userDataCache['gender'],
                    'birthday' => $userDataCache['birthday'], 
                    'password' => $userDataCache['password'],
                ]
            );

            $token = $user->createToken('api_token')->accessToken;

            Cache::forget($key);

            return ['user' => $user, 'token' => $token];
        });
    }

    public function login(LoginDTO $data)
    {
        $phone = $this->phoneService->normalize($data->phone);

        $user = User::where('phone', $phone)->first();

        if (!$user || !Hash::check($data->password, $user->password))
            throw new AuthenticationException();

        $token = $user->createToken('api_token')->accessToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout()
    {
         $token = auth('mobile')->user()->token();

        auth('mobile')->user()
            ->deviceTokens()
            ->where('oauth_access_token_id', $token->id)
            ->delete();

        $token->revoke();
    }

    public function resendOtp(ResendOtpDTO $data): string
    {
        $phone = $this->phoneService->normalize($data->phone);

        return Cache::lock("otp_resend_{$phone}_{$data->session_id}", 5)
            ->block(3, function () use ($data, $phone) {

                $otp = OtpCode::query()
                    ->where('session_id', $data->session_id)
                    ->where('phone', $phone)
                    ->where('is_used', false)
                    ->first();

                if (! $otp) {
                    throw ValidationException::withMessages([
                        'phone' => [__('auth.invalid_credentials')]
                    ]);
                }

                $latestOtp = OtpCode::query()
                    ->where('phone', $phone)
                    ->where('type', $otp->type)
                    ->latest('id')
                    ->first();

                if (! $latestOtp || $latestOtp->isNot($otp)) {
                    throw ValidationException::withMessages([
                        'phone' => [__('auth.invalid_credentials')]
                    ]);
                }

                $registrationData = null;

                if ($otp->type === 'registration') {
                    $registrationData = $this->transferRegistrationData($otp->session_id);
                }

                $newSessionId = $this->otp->generateOtp(
                    $otp->phone,
                    $otp->type
                );

                if ($otp->type === 'registration') {
                    Cache::put(
                        "reg_data_{$newSessionId}",
                        $registrationData,
                        now()->addMinutes(10)
                    );
                }

                return $newSessionId;
            });
    }

    private function transferRegistrationData(string $oldSessionId): array
    {
        $data = Cache::get("reg_data_{$oldSessionId}");
        
        if (! $data) {
            throw ValidationException::withMessages([
                'session_id' => [__('auth.invalid_registration_state')]
            ]);
        }

        return $data;
    }
}
