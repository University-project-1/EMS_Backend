<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\AnnouncementDTO;
use App\DTOs\SystemUser\UpdateAnnouncementDTO;
use App\Enum\AnnouncementNotificationAction;
use App\Enum\AnnouncementReceiverType;
use App\Enum\SystemUserType;
use App\Models\Announcement;
use App\Models\SystemUser;
use App\Models\User;
use App\Notifications\SystemUser\Shared\AnnouncementNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AnnouncementService
{
    public function create(AnnouncementDTO $dto): Announcement
    {
        return DB::transaction(function () use ($dto): Announcement {
            $announcement = Announcement::create($dto->toArray());

            $this->replaceMedia($announcement, $dto->media);
            $this->notifyAfterCommit($announcement, AnnouncementNotificationAction::Created);

            return $announcement->refresh();
        });
    }

    public function edit(Announcement $announcement, UpdateAnnouncementDTO $dto): Announcement
    {
        return DB::transaction(function () use ($announcement, $dto): Announcement {
            $announcement->update($dto->updatePayload());

            $this->replaceMedia($announcement, $dto->media);

            $announcement = $announcement->refresh();
            $this->notifyAfterCommit($announcement, AnnouncementNotificationAction::Updated);

            return $announcement;
        });
    }

    private function replaceMedia(Announcement $announcement, ?UploadedFile $media): void
    {
        if (! $media instanceof UploadedFile) {
            return;
        }

        $announcement->clearMediaCollection('announcements');
        $announcement->addMedia($media)->toMediaCollection('announcements');
    }

    private function notifyAfterCommit(Announcement $announcement,AnnouncementNotificationAction $action): void {
        if (! $announcement->is_active) {
            return;
        }

        DB::afterCommit(function () use ($announcement, $action): void {
            $notification = new AnnouncementNotification($announcement, $action);

            match ($this->receiverType($announcement)) {
                AnnouncementReceiverType::Visitor => $this->notifyQuery(
                    User::query(),
                    $notification
                ),
                AnnouncementReceiverType::Exhibitor => $this->notifyQuery(
                    SystemUser::query()->where('type', SystemUserType::EXHIBITOR->value),
                    $notification
                ),
                AnnouncementReceiverType::All => $this->notifyAll($notification),
            };
        });
    }

    private function notifyAll(AnnouncementNotification $notification): void
    {
        $this->notifyQuery(User::query(), $notification);
        $this->notifyQuery(
            SystemUser::query()->where('type', SystemUserType::EXHIBITOR->value),
            $notification
        );
    }

    private function notifyQuery(Builder $query, AnnouncementNotification $notification): void
    {
        $query->orderBy('id')->chunkById(
            500,
            function (Collection $recipients) use ($notification): void {
                Notification::send($recipients, $notification);
            }
        );
    }

    private function receiverType(Announcement $announcement): AnnouncementReceiverType
    {
        return match (strtolower($announcement->receiver)) {
            AnnouncementReceiverType::Visitor->value, 'visitors' => AnnouncementReceiverType::Visitor,
            AnnouncementReceiverType::Exhibitor->value, 'exhibitors' => AnnouncementReceiverType::Exhibitor,
            AnnouncementReceiverType::All->value => AnnouncementReceiverType::All,
            default => throw new \LogicException('Invalid announcement receiver type.'),
        };
    }
}
