<?php

namespace App\Services\Mobile;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\{Cache, DB, Hash};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected OtpService $otp) {}

    public function register(array $data)
    {
        $user = User::where('phone', $data['phone'])->orWhere('email', $data['email'])->first();
        if ($user) {
            throw ValidationException::withMessages([
                'phone' => ['invalid registration state']
            ]);
        }

        $registrationId = $this->otp->generateOtp($data, "registration");

        Cache::put("reg_data_{$registrationId}", [
            'phone' => $data['phone'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'job' => $data['job'],
            'location' => $data['location'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],  
            'password' => $data['password']
        ], now()->addMinutes(10));

        return $registrationId;
    }

    public function verifyRegister(array $data)
    {
        return DB::transaction(function () use ($data) {
            $this->otp->verifyOtp($data, "registration");

            $key = "reg_data_{$data['registration_id']}";

            $userDataCache = Cache::get($key);

            if (!$userDataCache)
                throw ValidationException::withMessages([
                    'user' => ['Registration session expired or not found']
                ]);

            $userIsFound = User::where('phone', $userDataCache['phone'])->first();
            if ($userIsFound) {
                throw ValidationException::withMessages([
                    'phone' => ['invalid registration state']
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

    public function login(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user || !Hash::check($data['password'], $user->password))
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

    public function forgotPassword(array $data)
    {
        $userExists = User::where('phone', $data['phone'])->exists();

        if ($userExists) {
            return $this->otp->generateOtp($data, "password_reset");
        }

        return Str::uuid()->toString();
    }

    public function verifyForgotPasswordOtp(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid credentials']
            ]);
        }

        $data['registration_id'] = $data['reset_id'];

        $this->otp->verifyOtp($data, "password_reset");

        $tempToken = Str::random(60);

        Cache::put("password_reset_token_{$tempToken}", [
            'phone' => $data['phone']
        ], now()->addMinutes(10));

        return $tempToken;
    }

    public function resetPassword(array $data)
    {
        $key = "password_reset_token_{$data['reset_token']}";
        $tokenDataCache = Cache::get($key);

        if (!$tokenDataCache) {
            throw ValidationException::withMessages([
                'token' => ['The password reset token is invalid or has expired.']
            ]);
        }

        $user = User::where('phone', $tokenDataCache['phone'])->firstOrFail();

        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        // return to this and sure it is working correctly
        $user->tokens()->update([
            'revoked' => true
        ]);

        Cache::forget($key);
    }

    public function resendOtp(array $data){
        $otp = OtpCode::query()
            ->where('session_id', $data['session_id'])
            ->where('phone' , $data['phone'])
            ->where('is_used', false)
            ->first();

        if (!$otp) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid credentials']
            ]);
        }

        $latestOtp = OtpCode::query()
            ->where('phone', $data['phone'])
            ->where('type', $otp->type)
            ->latest()
            ->first();

        if ($latestOtp->id !== $otp->id){
            throw ValidationException::withMessages([
                'phone' => ['Invalid credentials']
            ]);
        }

        return $this->otp->generateOtp(['phone' => $otp->phone], $otp->type);
    }
}
