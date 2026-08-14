<?php

namespace App\Services\Mobile;

use App\Enum\LeadInterestNotificationType;
use App\Models\Booth;
use App\Models\Event;
use App\Models\User;
use App\Notifications\Mobile\LeadInterestNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadInterestNotificationDispatcher
{
    /** @param Collection<int, User> $recipients */
    public function send(Collection $recipients, Booth|Event $content, LeadInterestNotificationType $type,): void {
        foreach ($recipients as $recipient) {
            $wasRecorded = DB::table('lead_interest_notification_deliveries')->insertOrIgnore([
                'user_id' => $recipient->getKey(),
                'notifiable_type' => $content::class,
                'notifiable_id' => $content->getKey(),
                'type' => $type->value,
                'sent_at' => now(),
            ]);

            if ($wasRecorded === 0) {
                continue;
            }

            $recipient->notify(new LeadInterestNotification($content, $type));
        }
    }
}
