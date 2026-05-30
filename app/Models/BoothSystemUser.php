<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['booth_id', 'system_user_id', 'assigned_by'])]
class BoothSystemUser extends Model
{
    protected $table = 'booth_system_users';

    public $timestamps = false;
}
