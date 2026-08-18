<?php

namespace App\Enum;

enum RequestRejectionReason: string
{
    case EVENT_EXPIRED = 'event_expired';
    case EVENT_SCHEDULE_CONFLICT = 'event_schedule_conflict';
    case BOOTH_CONFLICT = 'booth_conflict';
}
