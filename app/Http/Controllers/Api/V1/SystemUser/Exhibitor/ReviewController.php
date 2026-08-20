<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\UserResource;
use App\Http\Resources\SystemUser\Exhibitor\ReviewResource;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Review;
use App\Services\Mobile\ReviewService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Support\Facades\Gate;

#[Group('SystemUser/Exhibitor/Reviews')]
class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * event reviwes
     */
    #[QueryParameter('filter[search]', type: 'string', description: 'Search by reviewer name.')]
    #[QueryParameter('filter[rating]', type: 'integer', description: 'Filter by rating (1-5).')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by created_at or rating. Use -created_at for descending.')]
    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15, Max: 100.')]
    public function eventReviews(Event $event)
    {
        Gate::authorize('viewReviews', $event);

        $perPage = min(max(request()->integer('per_page', 15), 1), 100);

        $reviews = $this->reviewService->reviews($event, $perPage);

        return successResponse([
            'statistics' => $this->reviewService->getStatistics($event),
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * Booth reviews.
     */
    #[QueryParameter('filter[search]', type: 'string', description: 'Search by reviewer name.')]
    #[QueryParameter('filter[rating]', type: 'integer', description: 'Filter by rating (1-5).')]
    #[QueryParameter('sort', type: 'string', description: 'Sort by created_at or rating. Use -created_at for descending.')]
    #[QueryParameter('per_page', type: 'integer', description: 'Number of items per page. Default: 15, Max: 100.')]
    public function boothReviews(Booth $booth)
    {
        Gate::authorize('viewReviews', $booth);

        $perPage = min(max(request()->integer('per_page', 15), 1), 100);

        $reviews = $this->reviewService->reviews($booth, $perPage);

        return successResponse([
            'statistics' => $this->reviewService->getStatistics($booth),
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * Event review statistics.
     */
    public function eventStatistics(Event $event)
    {
        Gate::authorize('viewReviews', $event);

        return successResponse($this->reviewService->getStatistics($event));
    }

    /**
     * Booth review statistics.
     */
    public function boothStatistics(Booth $booth)
    {
        Gate::authorize('viewReviews', $booth);

        return successResponse($this->reviewService->getStatistics($booth));
    }

    /**
     * show reviewer details
     */
    public function reviewerDetails(Review $review)
    {
        $review->loadMissing(['reviewable', 'user']);

        Gate::authorize('viewReviews', $review->reviewable);

        return successResponse(UserResource::make($review->user));
    }
}
