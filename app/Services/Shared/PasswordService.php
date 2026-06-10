<?php

namespace App\Services\Shared;

use App\DTOs\Mobile\UpdatePasswordDTO;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordService
{
    public function updatePassword(Authenticatable $user, UpdatePasswordDTO $data)
    {
        if (!Hash::check($data->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password does not match our records.'],
            ]);
        }

        if (Hash::check($data->new_password, $user->password)) {
            throw ValidationException::withMessages([
                'new_password' => [
                    'New password must be different from current password.'
                ]
            ]);
        }

        $user->update([
            'password' => $data->new_password
        ]);

        $user->tokens()
            ->where('id', '!=', $user->token()->id)
            ->update([
                'revoked' => true
            ]);

        $user->deviceTokens()->where('oauth_access_token_id', '!=', $user->token()->id)->delete();
    }
}
