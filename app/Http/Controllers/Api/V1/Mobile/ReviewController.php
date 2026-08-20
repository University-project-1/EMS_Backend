<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\DTOs\Mobile\ReviewDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\StoreReviewRequest;
use App\Http\Resources\Mobile\ReviewResource;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Review;
use App\Services\Mobile\ReviewService;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;

#[Group('Visitor/Review')]
class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * store
     */
    public function store(StoreReviewRequest $request)
    {
        $this->reviewService->store(ReviewDTO::fromRequest($request->validated()));

        return successResponse();
    }

    /**
     * reviews on booth
     */
    #[QueryParameter('per_page', 'Number of reviews per page (maximum 100)', required: false, type: 'integer')]
    public function boothReviews(Request $request, Booth $booth)
    {
        $reviews = $this->reviewService->boothReviews(
            $booth,
            $request->integer('per_page', 10),
        );

        return successResponse([
            'avg_reviews' => $reviews['avgRating'],
            'reviews_count' => $reviews['reviweCount'],
            'current_user_review' => $reviews['currentUserReview']
                ? new ReviewResource($reviews['currentUserReview'])
                : null,
            'reviews' => ReviewResource::collection($reviews['reviews']),
        ]);
    }

    /**
     * reviews on event
     */
    #[QueryParameter('per_page', 'Number of reviews per page (maximum 100)', required: false, type: 'integer')]
    public function eventReviews(Request $request, Event $event)
    {
        $reviews = $this->reviewService->eventReviews(
            $event,
            $request->integer('per_page', 10),
        );

        return successResponse([
            'current_user_review' => $reviews['currentUserReview']
                ? new ReviewResource($reviews['currentUserReview'])
                : null,
            'reviews' => ReviewResource::collection($reviews['reviews']),
        ]);
    }

    /**
     * delete
     */
    public function destroy(Review $review)
    {
        $this->reviewService->deleteReview($review);

        return successResponse();
    }
}
