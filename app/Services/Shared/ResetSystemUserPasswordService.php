<?php

namespace App\Services\Shared;

use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResetSystemUserPasswordService
{
    public function sendResetLink(array $data)
    {
        $status = Password::broker()->sendResetLink($data);

        return $status;
    }
    public function resetPassword(array $data)
    {
        $status = Password::broker()->reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status;
    }

    /**
     * Change password for a logged-in user.
     */
    public function changePassword(SystemUser $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // DB::table('oauth_access_tokens')->where('user_id', $user->id)->delete();
    }
}
