<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\LoginDTO;
use App\DTOs\SystemUser\RegisterDTO;
use App\Models\SystemUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function login(LoginDTO $dto){
        $exhibitor = SystemUser::where('email', $dto->email)->first();
        if(!Hash::check($dto->password, $exhibitor->password)){
            return ['error' => 'no match'];
        }
        $token = $exhibitor->createToken('exhibitor_token')->accessToken;
        return ['success', 'token'=>$token, 'user' => $exhibitor];
    }

    public function register(RegisterDTO $dto){
        return DB::transaction(function() use ($dto){
            $exhibitor = SystemUser::updateOrCreate(
                ['name' => $dto->name],
                [
                    'email' => $dto->email,
                    'password' => Hash::make($dto->password),
                ]
            );
            event(new Registered($exhibitor));

            $token = $exhibitor->createToken('exhibitor_token')->accessToken;
            return [
                'user'  => $exhibitor,
                'token' => $token,
            ];
        });
    }

    public function verifyEmail(SystemUser $user, string $hash): bool
    {
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return true;
        }

        return false;
    }

}
