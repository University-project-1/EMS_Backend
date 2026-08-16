<?php

namespace App\Services\Mobile;

use App\Enum\Status;
use App\Filter\BoothSearchFilter;
use App\Models\Booth;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SavedService
{
    public function toggleSave(Model $model): void
    {
        $user = auth('mobile')->user();
        $saved = $user->savedItems()
            ->where('savedable_type', $model::class)
            ->where('savedable_id', $model->id)
            ->first();

        if ($saved) {
            $saved->delete();

            return;
        }

        $this->ensureSaveEligibility($model);

        $user->savedItems()->create([
            'savedable_type' => $model::class,
            'savedable_id' => $model->id,
        ]);
    }

    public function savedBooths(int $perPage)
    {
        return QueryBuilder::for(Booth::class)
            ->whereHas('savedItems', function ($query) {
                $query->where('user_id', auth('mobile')->id());
            })
            ->withExists([
                'savedItems as is_saved' => function ($query) {
                    $query->where('user_id', auth('mobile')->id());
                },
            ])
            ->allowedFilters(
                AllowedFilter::exact('business_sector', 'company.business_sector'),
                AllowedFilter::partial('number'),
                AllowedFilter::custom('search', new BoothSearchFilter)
            )
            ->allowedIncludes('company', 'hall', 'company.logoMedia')
            ->cursorPaginate($perPage);
    }

    private function ensureSaveEligibility(Model $model): void
    {
        if ($model instanceof Event && $model->status !== Status::APPROVED) {
            throw ValidationException::withMessages([
                'event_id' => [__('saved.event_not_approved')],
            ]);
        }

        if ($model instanceof Booth && $model->company_id === null) {
            throw ValidationException::withMessages([
                'booth_id' => [__('saved.booth_not_booked')],
            ]);
        }
    }
}
