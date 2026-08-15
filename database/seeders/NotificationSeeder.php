<?php

namespace Database\Seeders;

use App\Enum\SystemUserType;
use App\Models\Announcement;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\Event;
use App\Models\Report;
use App\Models\Review;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->removeLegacyNotifications();

        $event = Event::query()->first();
        $booth = Booth::query()->first();
        $boothRequest = BoothRequest::query()->first();
        $announcement = Announcement::query()->first();
        $report = Report::query()->first();
        $review = Review::query()->first();

        $admins = SystemUser::query()
            ->where('type', SystemUserType::ADMIN)
            ->get();
        $exhibitors = SystemUser::query()
            ->where('type', SystemUserType::EXHIBITOR)
            ->get();
        $visitors = User::query()->get();

        $this->seed($admins, array_filter([
            $this->notification('report_created', 'notifications.report_created_title', 'notifications.report_created_body', $report),
            $this->notification('event_booking_request_created', 'notifications.booking_request_created_title', 'notifications.booking_request_created_body', $event),
            $this->notification('booth_booking_request_created', 'notifications.booking_request_created_title', 'notifications.booking_request_created_body', $boothRequest),
        ]));

        $this->seed($exhibitors, array_filter([
            $this->notification('event_approved', 'notifications.event_approved_title', 'notifications.event_approved_body', $event),
            $this->notification('event_rejected', 'notifications.event_rejected_title', 'notifications.event_rejected_body', $event, read: true),
            $this->notification('booth_approved', 'notifications.booth_approved_title', 'notifications.booth_approved_body', $boothRequest),
            $this->notification('booth_rejected', 'notifications.booth_rejected_title', 'notifications.booth_rejected_body', $boothRequest, read: true),
            $this->notification('review_created', 'notifications.review_created_title', 'notifications.review_created_body', $review),
            $this->announcementNotification($announcement),
        ]));

        $this->seed($visitors, array_filter([
            $this->announcementNotification($announcement, read: true),
            $this->notification('event_reminder', 'notifications.event_reminder_title', 'notifications.event_reminder_body', $event),
            $this->notification('company_booth_created', 'notifications.company_booth_created_title', 'notifications.company_booth_created_body', $booth, ['target_type' => Booth::class]),
            $this->notification('company_event_created', 'notifications.company_event_created_title', 'notifications.company_event_created_body', $event, ['target_type' => Event::class]),
            $this->notification('organizer_event_created', 'notifications.organizer_event_created_title', 'notifications.organizer_event_created_body', $event, ['target_type' => Event::class]),
        ]));
    }

    /**
     * @param  iterable<int, SystemUser|User>  $notifiables
     * @param  array<int, array{data: array<string, mixed>, read: bool}>  $notifications
     */
    private function seed(iterable $notifiables, array $notifications): void
    {
        foreach ($notifiables as $notifiable) {
            foreach ($notifications as $notification) {
                $databaseNotification = $notifiable->notifications()
                    ->firstOrNew(['type' => $notification['data']['type']]);

                if (! $databaseNotification->exists) {
                    $databaseNotification->id = (string) Str::uuid();
                }

                $databaseNotification->data = $notification['data'];
                $databaseNotification->read_at = $notification['read'] ? now()->subHours(2) : null;
                $databaseNotification->save();
            }
        }
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array{data: array<string, mixed>, read: bool}|null
     */
    private function notification(string $type, string $title, string $body, ?Model $target, array $extra = [], bool $read = false): ?array
    {
        if (! $target) {
            return null;
        }

        return [
            'data' => array_merge([
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'target_id' => (string) $target->getKey(),
            ], $extra),
            'read' => $read,
        ];
    }

    /**
     * @return array{data: array<string, mixed>, read: bool}|null
     */
    private function announcementNotification(?Announcement $announcement, bool $read = false): ?array
    {
        if (! $announcement) {
            return null;
        }

        return [
            'data' => [
                'type' => 'announcement',
                'title' => $announcement->title,
                'body' => $announcement->description,
                'target_id' => (string) $announcement->getKey(),
            ],
            'read' => $read,
        ];
    }

    private function removeLegacyNotifications(): void
    {
        SystemUser::query()->each(function (SystemUser $systemUser): void {
            $systemUser->notifications()
                ->whereIn('type', ['new_message', 'approval_request', 'welcome'])
                ->delete();
        });

        User::query()->each(function (User $user): void {
            $user->notifications()
                ->whereIn('type', ['new_message', 'approval_request', 'welcome'])
                ->delete();
        });

    }
}
