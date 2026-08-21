<?php

namespace App\Services\Shared;

use App\Enum\Status;
use App\Filter\VolunteerApplicationSearchFilter;
use App\Jobs\SendVolunteerAcceptanceWhatsappJob;
use App\Models\SystemUser;
use App\Models\VolunteerApplication;
use App\Services\Mobile\PhoneService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VolunteerApplicationService
{
    public function __construct(private readonly PhoneService $phoneService) {}

    public function submit(array $attributes, UploadedFile $cv): VolunteerApplication
    {
        return DB::transaction(function () use ($attributes, $cv): VolunteerApplication {
            $application = VolunteerApplication::query()->create([
                ...$attributes,
                'phone' => $this->phoneService->normalize((string) $attributes['phone']),
                'privacy_consent_at' => now(),
            ]);

            $application
                ->addMedia($cv)
                ->usingName(pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME))
                ->toMediaCollection(VolunteerApplication::CV_COLLECTION);

            return $application;
        });
    }

    public function paginateForAdministration(int $perPage): LengthAwarePaginator
    {
        return QueryBuilder::for(VolunteerApplication::class)
            ->allowedFilters(
                AllowedFilter::exact('status'),
                AllowedFilter::custom('search', new VolunteerApplicationSearchFilter),
            )
            ->allowedSorts('created_at', 'full_name', 'status')
            ->defaultSort('-created_at')
            ->paginate($perPage);
    }

    public function statistics(): array
    {
        $statistics = VolunteerApplication::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending', [Status::PENDING->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved', [Status::APPROVED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected', [Status::REJECTED->value])
            ->firstOrFail();

        return [
            'total' => (int) $statistics->total,
            'pending' => (int) $statistics->pending,
            'approved' => (int) $statistics->approved,
            'rejected' => (int) $statistics->rejected,
        ];
    }

    public function approve(VolunteerApplication $application, SystemUser $reviewer, ?string $reviewNote): VolunteerApplication
    {
        return DB::transaction(function () use ($application, $reviewer, $reviewNote): VolunteerApplication {
            $application = VolunteerApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->getKey());

            $this->ensurePending($application);

            $application->update([
                'status' => Status::APPROVED,
                'reviewed_by' => $reviewer->getKey(),
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
                'whatsapp_notification_failed_at' => null,
                'whatsapp_notification_error' => null,
            ]);

            SendVolunteerAcceptanceWhatsappJob::dispatch($application->getKey())->afterCommit();

            return $application->fresh(['media', 'reviewer']);
        });
    }

    public function reject(VolunteerApplication $application, SystemUser $reviewer, ?string $reviewNote): VolunteerApplication
    {
        return DB::transaction(function () use ($application, $reviewer, $reviewNote): VolunteerApplication {
            $application = VolunteerApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->getKey());

            $this->ensurePending($application);

            $application->update([
                'status' => Status::REJECTED,
                'reviewed_by' => $reviewer->getKey(),
                'reviewed_at' => now(),
                'review_note' => $reviewNote,
            ]);

            return $application->fresh(['media', 'reviewer']);
        });
    }

    public function cvFor(VolunteerApplication $application): Media
    {
        $media = $application->getFirstMedia(VolunteerApplication::CV_COLLECTION);

        if (! $media instanceof Media) {
            throw new NotFoundHttpException(__('volunteer.errors.cv_not_found'));
        }

        return $media;
    }

    private function ensurePending(VolunteerApplication $application): void
    {
        if (! $application->isPending()) {
            throw new ConflictHttpException(__('volunteer.errors.already_reviewed'));
        }
    }
}
