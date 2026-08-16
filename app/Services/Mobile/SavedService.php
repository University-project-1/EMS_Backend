<?php

namespace App\Services\Mobile;

use App\Filter\BoothSearchFilter;
use App\Models\Booth;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SavedService
{
    public function toggleSave(Model $model)
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
                AllowedFilter::exact('business_sector','company.business_sector'),
                AllowedFilter::custom('search', new BoothSearchFilter())
            )
            ->allowedIncludes('company','hall','company.logoMedia')
            ->cursorPaginate($perPage);
    }
}