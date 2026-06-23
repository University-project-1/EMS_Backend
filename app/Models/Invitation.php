<?php

namespace App\Models;

use App\Enum\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['invitable_type', 'invitable_id', 'email', 'token', 'status', 'expires_at'])]
class Invitation extends Model
{
    protected function casts():array
    {
        return [
            'status' => Status::class,
        ];
    }

    public function invitable(){
        return $this->morphTo();
    }
}
