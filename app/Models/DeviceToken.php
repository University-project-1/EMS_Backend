<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['tokenable_type', 'tokenable_id', 'fcm_token', 'device_type', 'oauth_access_token_id'])]
class DeviceToken extends Model
{
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }
}
