<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['location', 'start_time', 'end_time', 'duration'])]
#[Table('bus_catalog')]
class BusCatalog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }
}
