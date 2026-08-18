<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Filter\CompanyNameFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\BoothResource;
use App\Models\Booth;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

#[Group('Visitor/Booth')]
class BoothController extends Controller
{
    /**
     * all
     */
    #[QueryParameter('filter[number]', 'Filter booths by exact booth number', required: false, type: 'string')]
    #[QueryParameter('filter[company_name]', 'Filter booths by company name', required: false, type: 'string')]
    #[QueryParameter('include', 'Include related resources (company, hall)', required: false, type: 'string')]
    public function index(){
        $booths = QueryBuilder::for(Booth::class)
            ->allowedFilters(
                AllowedFilter::exact('number'),
                AllowedFilter::custom('company_name', new CompanyNameFilter()),
            )
            ->allowedIncludes('company', 'hall')
            ->withExists([
                'savedItems as is_saved' => fn (Builder $savedItems): Builder => $savedItems
                    ->where('user_id', auth('mobile')->user()->getKey()),
                'reviews as is_review' => fn (Builder $reviews): Builder => $reviews
                    ->where('user_id', auth('mobile')->user()->getKey()),
            ])
            ->cursorPaginate(10);
        return successResponse(
            data: BoothResource::collection($booths),
            message: 'booths returned successfully',
        );
    }

    /**
     * show
     */
    public function show(Booth $booth){
        $booth->loadMissing(['hall', 'company'])->loadExists([
            'reviews as is_review' => fn (Builder $reviews): Builder => $reviews
                ->where('user_id', auth('mobile')->user()->getKey()),
            'savedItems as is_saved' => fn (Builder $savedItems): Builder => $savedItems
                    ->where('user_id', auth('mobile')->user()->getKey())
            ]);
        return successResponse(
            data: new BoothResource($booth),
            message: 'booth returned successfully',
        );
    }
}

