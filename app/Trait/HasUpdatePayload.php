<?php

namespace App\Trait;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasUpdatePayload
{
    public function updatePayload(): array
    {
        return collect(get_object_vars($this))
            ->except(['payload'])
            ->mapWithKeys(function ($value, string $property) {
                if ($value instanceof UploadedFile) {
                    return [];
                }

                $payloadKeys = array_keys($this->payload());

                if (in_array($property, $payloadKeys, true)) {
                    return [$property => $value];
                }

                $snakeCaseProperty = Str::snake($property);
                if (in_array($snakeCaseProperty, $payloadKeys, true)) {
                    return [$snakeCaseProperty => $value];
                }

                return [];
            })
            ->toArray();
    }
}
