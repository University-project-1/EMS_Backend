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

#[Group('Visitor/Review')]
class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService){}

    /**
     * store
     */
    public function store(StoreReviewRequest $request){
        $this->reviewService->store(ReviewDTO::fromRequest($request->validated()));

        return successResponse(); 
    }

    /**
     * reviews on booth
     */
    public function boothReviews(Booth $booth){
        $reviews = $this->reviewService->boothReviews($booth);
        
        return successResponse(ReviewResource::collection($reviews));
    }

    /**
     * reviews on event
     */
    public function eventReviews(Event $event){
        $reviews = $this->reviewService->eventReviews($event);
        
        return successResponse(ReviewResource::collection($reviews));
    }

    /**
     * delete
     */
    public function destroy(Review $review){
        $this->reviewService->deleteReview($review);

        return successResponse();
    }
}
