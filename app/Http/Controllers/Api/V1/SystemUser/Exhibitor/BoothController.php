<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\DTOs\SystemUser\BoothRequestDTO;
use App\DTOs\SystemUser\CompanyDTO;
use App\Filter\BookedBoothFilter;
use App\Filter\MaxFilter;
use App\Filter\MinFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemUser\Exhibitor\StoreBoothRequestRequest;
use App\Http\Resources\SystemUser\Shared\BoothRequestResource;
use App\Http\Resources\SystemUser\Shared\BoothResource;
use App\Models\Booth;
use App\Services\SystemUser\Exhibitor\BoothRequestService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('SystemUser/Exhibitor/Booths')]
class BoothController extends Controller
{
    public function __construct(
        private readonly BoothRequestService $boothRequestService,
    ){}
    /**
     * all
     */
    #[QueryParameter('filter[number]', 'Filter booths by exact booth number', required: false, type: 'string')]
    #[QueryParameter('filter[booked]', 'Filter booths by booking status', required: false, type: 'boolean')]
    #[QueryParameter('filter[hall_id]', 'Filter booths by hall_id', required: false, type: 'number')]
    #[QueryParameter('filter[hall_type]', 'Filter booths by hall type', required: false, type: 'string(enum)')]
    #[QueryParameter('filter[min_price]', 'Filter booths by minimum price', required: false, type: 'number')]
    #[QueryParameter('filter[max_price]', 'Filter booths by maximum price', required: false, type: 'number')]
    #[QueryParameter('filter[min_area]', 'Filter booths by minimum area', required: false, type: 'number')]
    #[QueryParameter('filter[max_area]', 'Filter booths by maximum area', required: false, type: 'number')]
    #[QueryParameter('include', 'Include related resources (company, hall)', required: false, type: 'string')]
    #[QueryParameter('sort', 'Sort results by field (price, area). Prefix with - for descending order', required: false, type: 'string')]
    public function index(){
        $booths = QueryBuilder::for(Booth::class)
            ->allowedFilters(
                AllowedFilter::exact('number'),
                AllowedFilter::exact('hall_id'),
                AllowedFilter::exact('hall_type', 'hall.type'),
                AllowedFilter::custom('booked', new BookedBoothFilter()),
                AllowedFilter::custom('min_price', new MinFilter(), 'price'),
                AllowedFilter::custom('max_price', new MaxFilter(), 'price'),
                AllowedFilter::custom('min_area', new MinFilter(), 'area'),
                AllowedFilter::custom('max_area', new MaxFilter(), 'area')
            )
            ->allowedIncludes('company', 'hall')
            ->allowedSorts('price', 'area')
            ->get();
        return successResponse(
            data: BoothResource::collection($booths),
            message: __('booth.list_success'),
        );
    }
    /**
     * show
     */
    public function show(Booth $booth){
        $booth->loadMissing(['hall', 'company']);
        return successResponse(
            data: new BoothResource($booth),
            message: __('booth.show_success'),
        );
    }
    /**
     * request booth booking
     */
    public function book(StoreBoothRequestRequest $request)
    {
        $validated = $request->validated();
        $dto = BoothRequestDTO::fromRequest($validated);
        $companyDto = isset($validated['new_company'])
            ? CompanyDTO::fromRequest($validated['new_company'])
            : null;
        $boothRequest = $this->boothRequestService->confirmBoothBooking($request->user('system'), $dto, $companyDto);

        return successResponse(
            data: new BoothRequestResource($boothRequest),
            message: 'booking confirmed successfully, needs admin confirmation',
        );
    }
    /**
     * My booths
     */
    public function ownedBooths(Request $request)
    {
        $userId = $request->user('system')->id;

        $booths = Booth::with([
            'company',
            'hall',
            'boothRequests' => function ($query) {
                $query->where('status', 'APPROVED');
            },
            'boothRequests.attachedServices'
        ])
        ->where(function ($query) use ($userId) {
            $query->whereHas('systemUsers', fn($q) => $q->whereKey($userId))
                ->orWhereHas('company.systemUsers', fn($q) => $q->whereKey($userId));
        })
        ->latest()
        ->paginate(5);

        return successResponse(
            data: BoothResource::collection($booths),
            message: __('booth.list_success')
        );
    }
}
