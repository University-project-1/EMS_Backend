<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\ReviewDTO;
use App\Enum\Status;
use App\Filter\ReviewSearchFilter;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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

    public function reviews(Model $reviewable, int $perPage = 15)
    {
        return QueryBuilder::for(Review::query()->whereMorphedTo('reviewable', $reviewable))
            ->allowedFilters(
                AllowedFilter::custom('search',new ReviewSearchFilter()),
                AllowedFilter::exact('rating'),
            )
            ->allowedSorts('created_at', 'rating')
            ->defaultSort('-created_at')
            ->with('user.media')
            ->paginate($perPage);
    }

    public function getStatistics(Model $reviewable): array
    {
        $statistics = $reviewable->reviews()
            ->selectRaw('COUNT(*) as total_reviews')
            ->selectRaw('COALESCE(AVG(rating), 0) as average_rating')
            ->selectRaw('SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star_reviews')
            ->first();

        return [
            'total_reviews' => (int) $statistics->total_reviews,
            'average_rating' => round((float) $statistics->average_rating, 1),
            'five_star_reviews' => (int) $statistics->five_star_reviews,
        ];
    }
}
