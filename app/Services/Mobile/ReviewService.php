<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\ReviewDTO;
use App\Enum\Status;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class ReviewService
{
    public function store(ReviewDTO $dto){
        $reviewable_type = null;
        $reviewable_id = null;
        if($dto->event_id){
            $reviewable_type = Event::class;
            $reviewable_id = $dto->event_id;
        }elseif($dto->booth_id){
            $reviewable_type = Booth::class;
            $reviewable_id = $dto->booth_id;
        }
        
        auth('mobile')->user()->reviews()->updateOrCreate(
            [
                'reviewable_id' => $reviewable_id, 
                'reviewable_type' => $reviewable_type
            ],
            [
                'rating' => $dto->rating,
                'comment' => $dto->comment,
            ]
        );
    }

    public function boothReviews(Booth $booth){
        if($booth->company_id === null){
            return throw new ModelNotFoundException();
        }

        return $booth->reviews()->latest()->with('user.media')->cursorPaginate(10);
    }

    public function eventReviews(Event $event){
        if($event->status != Status::APPROVED){
            return throw new ModelNotFoundException();
        }

        return $event->reviews()->latest()->with('user.media')->cursorPaginate(10);
    }

    public function deleteReview(Review $review){
        Gate::authorize('delete', $review);

        $review->delete();
    }
}
