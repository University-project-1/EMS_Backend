<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['phone', 'otp', 'type', 'attempts', 'is_used', 'expires_at', 'session_id'])]
class OtpCode extends Model
{
    //
}
