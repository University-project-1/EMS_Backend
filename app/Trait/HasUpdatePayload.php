<?php

namespace App\Trait;

use Illuminate\Http\UploadedFile;

trait HasUpdatePayload
{
    public function updatePayload(): array
    {
        return collect(get_object_vars($this))
            ->only(array_keys($this->payload()))
            ->reject(fn ($value) => $value instanceof UploadedFile)
            ->except(['payload'])
            ->toArray();
    }
}
