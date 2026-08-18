<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\LoginDTO;
use App\DTOs\SystemUser\RegisterDTO;
use App\Enum\SystemUserType;
use App\Models\Invitation;
use App\Models\SystemUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly InvitationService $invitationService,
    ) {}

    public function login(LoginDTO $dto)
    {
        $exhibitor = SystemUser::query()->where('email', $dto->email)->where('type', SystemUserType::EXHIBITOR)->first();
        if (! $exhibitor || ! Hash::check($dto->password, $exhibitor->password)) {
            throw new AuthenticationException;
        }

        if (! $exhibitor->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => [__(__('auth.email_not_verified'))],
            ]);
        }

        $token = $exhibitor->createToken('exhibitor_token')->accessToken;

        return ['success', 'token' => $token, 'user' => $exhibitor];
    }

    public function register(RegisterDTO $dto)
    {

        return DB::transaction(function () use ($dto) {
            $exhibitor = SystemUser::updateOrCreate(
                ['email' => $dto->email],
                [
                    'name' => $dto->name,
                    'password' => Hash::make($dto->password),
                ]
            );

            event(new Registered($exhibitor));
            $token = $exhibitor->createToken('exhibitor_token')->accessToken;

            return [
                'message' => 'auth.verification_sent',
                'user' => $exhibitor,
                'token' => $token,
            ];
        });
    }

    public function registerViaInvitation(Invitation $invitation, RegisterDTO $dto)
    {
        return DB::transaction(function () use ($invitation, $dto) {
            $exhibitor = SystemUser::updateOrCreate(
                ['email' => $invitation->email],
                [
                    'name' => $dto->name,
                    'password' => Hash::make($dto->password),
                ]
            );
            $exhibitor->markEmailAsVerified();
            $this->invitationService->approve($invitation);

            $token = $exhibitor->createToken('exhibitor_token')->accessToken;

            return [
                'message' => 'auth.email_verified',
                'user' => $exhibitor,
                'token' => $token,
            ];
        });
    }

    public function verifyEmail(SystemUser $user, string $hash)
    {
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw ValidationException::withMessages([
                'verification' => [__('validation.invalid_link')],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->markEmailAsVerified();

        event(new Verified($user));
    }

    public function resendVerificationEmail(SystemUser $user)
    {
        if ($user->hasVerifiedEmail()) {
            abort(400, __('validation.already_verified'));
        }

        $user->sendEmailVerificationNotification();
    }
}
