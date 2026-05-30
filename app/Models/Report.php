<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['reporter_type', 'reporter_id', 'reportable_type', 'reportable_id', 'title', 'description', 'resolved_by', 'status', 'admin_notes'])]
class Report extends Model
{
    public function reporter(): MorphTo
    {
        return $this->morphTo();
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'resolved_by');
    }
}
