<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\ReviewDTO;
use App\Enum\Status;
use App\Filter\ReviewSearchFilter;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Review;
use App\Notifications\SystemUser\Exhibitor\NewReviewNotification;
use App\Services\Shared\NotificationRecipientResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReviewService
{
    public function __construct(private readonly NotificationRecipientResolver $notificationRecipients) {}

    public function store(ReviewDTO $dto)
    {
        $reviewableType = $dto->event_id ? Event::class : Booth::class;
        $reviewableId = $dto->event_id ?: $dto->booth_id;

        return DB::transaction(function () use ($dto, $reviewableType, $reviewableId) {
            $review = auth('mobile')->user()->reviews()->updateOrCreate(
                ['reviewable_id' => $reviewableId, 'reviewable_type' => $reviewableType],
                ['rating' => $dto->rating, 'comment' => $dto->comment]
            );

            if (! $review->wasRecentlyCreated) {
                return $review;
            }

            $review->loadMissing('reviewable');
            $recipients = match (true) {
                $review->reviewable instanceof Event => $this->notificationRecipients->eventOwners($review->reviewable),
                $review->reviewable instanceof Booth => $this->notificationRecipients->boothCompanyMembers($review->reviewable),
                default => collect(),
            };

            DB::afterCommit(function () use ($recipients, $review): void {
                Notification::send(
                    $recipients->filter()->unique('id'),
                    new NewReviewNotification($review)
                );
            });

            return $review;
        });
    }

    public function boothReviews(Booth $booth)
    {
        if ($booth->company_id === null) {
            return throw new ModelNotFoundException;
        }

        return $booth->reviews()->latest()->with('user.media')->cursorPaginate(10);
    }

    public function eventReviews(Event $event)
    {
        if ($event->status != Status::APPROVED) {
            return throw new ModelNotFoundException;
        }

        return $event->reviews()->latest()->with('user.media')->cursorPaginate(10);
    }

    public function deleteReview(Review $review)
    {
        Gate::authorize('delete', $review);

        $review->delete();
    }

    public function reviews(Model $reviewable, int $perPage = 15)
    {
        return QueryBuilder::for(Review::query()->whereMorphedTo('reviewable', $reviewable))
            ->allowedFilters(
                AllowedFilter::custom('search', new ReviewSearchFilter),
                AllowedFilter::exact('rating'),
            )
            ->allowedSorts('created_at', 'rating')
            ->defaultSort('-created_at')
            ->with('user.media')
            ->paginate($perPage);
    }

    public function getStatistics(Booth|Event $reviewable): array
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
