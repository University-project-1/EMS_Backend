<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_id', 'system_user_id'])]
class CompanySystemUser extends Model
{
    protected $table = 'company_system_users';

    public $timestamps = false;
}
