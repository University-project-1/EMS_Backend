<?php

namespace App\Trait;

use Illuminate\Support\Str;

trait HasSnakeCaseArray
{
    public function toArray(): array
    {
        $properties = get_object_vars($this);
        $array = [];

        foreach ($properties as $key => $value) {
            $array[Str::snake($key)] = $value;
        }

        return $array;
    }
}
