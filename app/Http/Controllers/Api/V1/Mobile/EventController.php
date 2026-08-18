<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Enum\Status;
use App\Filter\EventDateFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\IndexEventRequest;
use App\Http\Resources\Shared\EventResource;
use App\Models\Company;
use App\Models\Event;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Visitor/Events')]
class EventController extends Controller
{
    /**
     * all
     */
    #[QueryParameter('filter[type]', 'Filter events by exact type', required: false, type: 'string')]
    #[QueryParameter('filter[title]', 'Search by partial title', required: false, type: 'string')]
    #[QueryParameter('filter[date]', 'Filter events overlapping the selected day (Y-m-d)', required: false, type: 'string')]
    #[QueryParameter('filter[saved]', 'Filter by the authenticated visitor schedule', required: false, type: 'boolean')]
    #[QueryParameter('per_page', 'Number of events per page (maximum 100)', required: false, type: 'integer')]
    #[QueryParameter('include', 'Include related resources (eventable, speakers)', required: false, type: 'string')]
    public function index(IndexEventRequest $request)
    {
        $user = $this->authenticatedUser($request);

        $events = QueryBuilder::for(
            Event::query()->where('status', Status::APPROVED->value)
        )
            ->allowedFilters(
                AllowedFilter::exact('type'),
                AllowedFilter::partial('title'),
                AllowedFilter::custom('date', new EventDateFilter),
                AllowedFilter::callback('saved', function (Builder $query, mixed $value) use ($user): void {
                    $savedByUser = fn (Builder $savedItems): Builder => $savedItems->where('user_id', $user->getKey());
                    if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                        $query->whereHas('savedItems', $savedByUser);

                        return;
                    }
                    $query->whereDoesntHave('savedItems', $savedByUser);
                }),
            )
            ->allowedIncludes('eventable', 'speakers')
            ->with('media')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withExists([
                'savedItems as is_saved' => fn (Builder $savedItems): Builder => $savedItems
                    ->where('user_id', $user->getKey()),
            ])
            ->orderBy('start_at')
            ->orderBy('id')
            ->cursorPaginate($request->integer('per_page', 10))
            ->withQueryString();

        return successResponse(EventResource::collection($events));
    }

    /**
     * show
     */
    public function show(Request $request, int $event)
    {
        $user = $this->authenticatedUser($request);

        $event = Event::query()
            ->where('status', Status::APPROVED->value)
            ->with(['media', 'speakers', 'eventable' => function (Relation $relation): void {
                if ($relation instanceof MorphTo) {
                    $relation->morphWith([
                        Company::class => ['logoMedia'],
                    ]);
                }
            }])
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews'])
            ->withExists([
                'savedItems as is_saved' => fn (Builder $savedItems): Builder => $savedItems
                    ->where('user_id', $user->getKey()),
            ])
            ->findOrFail($event);

        return successResponse(EventResource::make($event));
    }

    /**
     * Nearest upcoming events.
     */
    public function nearest()
    {
        $events = Event::query()
            ->where('status', Status::APPROVED->value)
            ->where('start_at', '>=', now())
            ->with(['speakers', 'eventable', 'media'])
            ->withAvg('reviews', 'rating')
            ->orderBy('start_at')
            ->orderBy('id')
            ->limit(5)
            ->get();

        return successResponse(EventResource::collection($events));
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user('mobile');
        abort_unless($user instanceof User, 401);

        return $user;
    }
}
