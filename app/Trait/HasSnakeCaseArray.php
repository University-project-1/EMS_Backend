<?php

namespace App\Trait;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasSnakeCaseArray
{
    public function toArray(): array
    {
        $properties = get_object_vars($this);
        $array = [];

        foreach ($properties as $key => $value) {
            if ($value instanceof UploadedFile) {
                continue;
            }
            if (is_array($value) && !empty($value) && current($value) instanceof UploadedFile) {
                continue;
            }

            $array[Str::snake($key)] = $value;
        }

        return $array;
    }
}
