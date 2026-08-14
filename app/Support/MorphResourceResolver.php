<?php

namespace App\Support;

use App\Http\Resources\Mobile\BoothResource;
use App\Http\Resources\Shared\EventResource;
use App\Models\Booth;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use InvalidArgumentException;

class MorphResourceResolver
{
    public static function resourceFor(Model $model): JsonResource
    {
        return match ($model::class) {
            Booth::class => new BoothResource($model),
            Event::class => new EventResource($model),
            default => throw new InvalidArgumentException('Unsupported morphable model: '.$model::class),
        };
    }
}
