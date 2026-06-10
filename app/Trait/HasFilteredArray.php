<?php

namespace App\Trait;

use Illuminate\Http\UploadedFile;

trait HasFilteredArray
{

    public function toFilteredArray(): array
    {
        $properties = get_object_vars($this);

        return array_filter($properties, function ($value) {
            return $value !== null && !($value instanceof UploadedFile);
        });
    }
}
