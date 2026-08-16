<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Filter\BoothProductsSearchFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\BoothProductResource;
use App\Models\Booth;
use App\Models\BoothProduct;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Visitor/Booth')]
class BoothProductController extends Controller
{
    /**
     * booth products
     */
    #[QueryParameter('filter[search]', 'Search products by name or description', type: 'string')]
    #[QueryParameter('sort', 'Allowed values: name, price, sort_order', type: 'string')]
    #[QueryParameter('per_page', 'Number of items per page. Default: 20, maximum: 50', type: 'integer')]
    public function index(Request $request, Booth $booth)
    {
        $approvedRequest = $booth->approvedBoothRequest()->first();

        if (! $approvedRequest) {
            return successResponse(BoothProductResource::collection(collect()),);
        }

        $products = QueryBuilder::for(BoothProduct::query()->where('booth_request_id', $approvedRequest->getKey()))
            ->allowedFilters(AllowedFilter::custom('search', new BoothProductsSearchFilter()))
            ->allowedSorts(
                AllowedSort::field('name'),
                AllowedSort::field('price'),
                AllowedSort::field('sort_order'),
            )
            ->defaultSort('sort_order')
            ->cursorPaginate(min(max($request->integer('per_page', 10), 1), 50));

        return successResponse(BoothProductResource::collection($products));
    }
}
