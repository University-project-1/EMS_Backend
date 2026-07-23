<?php

namespace App\Models;

use App\Enum\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['inviteable_type', 'inviteable_id', 'sender_id', 'email', 'token', 'status', 'expires_at'])]
class Invitation extends Model
{
    protected function casts():array
    {
        return [
            'status' => Status::class,
        ];
    }

    public function inviteable(){
        return $this->morphTo();
    }
    public function sender(){
        return $this->belongsTo(SystemUser::class, 'sender_id');
    }
    public function isValid(): bool
    {
        return $this->status === Status::PENDING && $this->expires_at && Carbon::parse($this->expires_at)->isFuture();
    }
}
