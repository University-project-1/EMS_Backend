<?php

namespace App\Services\Shared;

class FCMService
{
    public function store(array $data, string $guardName)
    {
        auth($guardName)->user()->deviceTokens ()->updateOrCreate(
            ['fcm_token' => $data['token']],
            [
                'oauth_access_token_id' => auth('mobile')->user()->token()->id,
                'device_type' => $data['device_type']
            ]
        );
    }
}
