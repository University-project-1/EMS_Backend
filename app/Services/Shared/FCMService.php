<?php

namespace App\Services\Shared;

use App\Models\SystemUser;
use App\Models\User;
use Laravel\Passport\AccessToken;

class FCMService
{
    /**
     * @param  array{token: string, device_type: string}  $data
     */
    public function store(array $data, string $guardName): void
    {
        $authenticatedUser = auth($guardName)->user();

        abort_unless($authenticatedUser instanceof SystemUser || $authenticatedUser instanceof User, 401);

        $accessToken = $authenticatedUser->currentAccessToken();

        abort_unless($accessToken instanceof AccessToken, 401);

        $authenticatedUser->deviceTokens()->updateOrCreate(
            ['fcm_token' => $data['token']],
            [
                'oauth_access_token_id' => $accessToken->oauth_access_token_id,
                'device_type' => $data['device_type'],
            ]
        );
    }
}
