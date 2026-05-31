<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_id', 'system_user_id'])]
#[Table('company_system_users')]
class CompanySystemUser extends Model
{
    public $timestamps = false;
}
