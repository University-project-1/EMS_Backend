<?php

namespace App\Services\SystemUser\Shared;

use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResetSystemUserPasswordService
{
    public function sendResetLink(array $data): string
    {
        return Password::broker()->sendResetLink($data);
    }

    public function resetPassword(array $data): string
    {
        return Password::broker()->reset(
            $data,
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password),
                ]);

                event(new PasswordReset($user));
            }
        );
    }

    public function changePassword(
        SystemUser $user,
        string $newPassword
    ): void {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
