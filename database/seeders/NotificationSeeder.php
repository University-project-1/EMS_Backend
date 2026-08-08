<?php

namespace Database\Seeders;

use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get();
        $systemUsers = SystemUser::query()->get();

        if ($users->isEmpty() || $systemUsers->isEmpty()) {
            return;
        }

        $firstEvent = null;
        $firstBooth = null;

        if (class_exists(\App\Models\Event::class)) {
            $firstEvent = \App\Models\Event::query()->first();
        }

        if (class_exists(\App\Models\Booth::class)) {
            $firstBooth = \App\Models\Booth::query()->first();
        }

        $notifications = [
            [
                'notifiable' => $users[0],
                'type' => 'welcome',
                'data' => [
                    'type' => 'welcome',
                    'title' => 'Welcome to EMS',
                    'body' => 'Thank you for joining the Exhibition Management System.',
                    'target_id' => null,
                ],
                'read_at' => null,
            ],
            [
                'notifiable' => $users[1],
                'type' => 'event_reminder',
                'data' => [
                    'type' => 'event_reminder',
                    'title' => 'Event reminder',
                    'body' => 'Your next event starts soon. Please check the schedule.',
                    'target_id' => $firstEvent?->getKey(),
                ],
                'read_at' => now(),
            ],
            [
                'notifiable' => $users[2],
                'type' => 'booth_update',
                'data' => [
                    'type' => 'booth_update',
                    'title' => 'Booth update',
                    'body' => 'A booth you follow has a new update.',
                    'target_id' => $firstBooth?->getKey(),
                ],
                'read_at' => null,
            ],
            [
                'notifiable' => $systemUsers->first(),
                'type' => 'report_created',
                'data' => [
                    'type' => 'report_created',
                    'title' => 'New report submitted',
                    'body' => 'A new report has been submitted and requires your review.',
                    'target_id' => null,
                ],
                'read_at' => null,
            ],
            [
                'notifiable' => $systemUsers->skip(1)->first() ?? $systemUsers->first(),
                'type' => 'user_message',
                'data' => [
                    'type' => 'user_message',
                    'title' => 'New message received',
                    'body' => 'You have received a new message from an exhibition participant.',
                    'target_id' => null,
                ],
                'read_at' => now(),
            ],
            [
                'notifiable' => $systemUsers->last(),
                'type' => 'booking_request',
                'data' => [
                    'type' => 'booking_request',
                    'title' => 'New booth booking request',
                    'body' => 'A visitor requested to book a booth. Please confirm the request.',
                    'target_id' => $firstBooth?->getKey(),
                ],
                'read_at' => null,
            ],
            [
                'notifiable' => $users[0],
                'type' => 'feedback_request',
                'data' => [
                    'type' => 'feedback_request',
                    'title' => 'Leave a review',
                    'body' => 'Please tell us about your experience at the latest exhibition.',
                    'target_id' => null,
                ],
                'read_at' => now(),
            ],
            [
                'notifiable' => $users[1],
                'type' => 'system_notice',
                'data' => [
                    'type' => 'system_notice',
                    'title' => 'System maintenance',
                    'body' => 'The EMS platform will undergo a short maintenance tonight.',
                    'target_id' => null,
                ],
                'read_at' => null,
            ],
            [
                'notifiable' => $users[2],
                'type' => 'event_cancellation',
                'data' => [
                    'type' => 'event_cancellation',
                    'title' => 'Event cancellation',
                    'body' => 'An event has been cancelled. Check your notifications for details.',
                    'target_id' => $firstEvent?->getKey(),
                ],
                'read_at' => now(),
            ],
            [
                'notifiable' => $systemUsers->first(),
                'type' => 'approval_request',
                'data' => [
                    'type' => 'approval_request',
                    'title' => 'Approval request pending',
                    'body' => 'A new request is waiting for your approval.',
                    'target_id' => null,
                ],
                'read_at' => null,
            ],
        ];

        foreach ($notifications as $notificationData) {
            $notifiable = $notificationData['notifiable'];
            unset($notificationData['notifiable']);

            $notifiable->notifications()->updateOrCreate(
                [
                    'type' => $notificationData['type'],
                    'data' => $notificationData['data'],
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'read_at' => $notificationData['read_at'],
                ],
            );
        }
    }
}
