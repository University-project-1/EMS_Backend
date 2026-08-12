<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['number', 'gender', 'svg_id', 'type'])]
class Facility extends Model
{
    public $timestamps = false;
    // cast type enum
}
