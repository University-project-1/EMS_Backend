<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['location', 'start_time', 'end_time', 'duration'])]
class BusCatalog extends Model
{
    protected $table = 'bus_catalog';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }
}
