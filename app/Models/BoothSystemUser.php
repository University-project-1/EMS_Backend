<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['booth_id', 'system_user_id', 'assigned_by'])]
#[Table('booth_system_users')]
class BoothSystemUser extends Model
{
    public const UPDATED_AT = null;
}
