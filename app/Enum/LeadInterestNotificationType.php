<?php

namespace App\Enum;

enum LeadInterestNotificationType: string
{
    case COMPANY_BOOTH_CREATED = 'company_booth_created';
    case COMPANY_EVENT_CREATED = 'company_event_created';
    case ORGANIZER_EVENT_CREATED = 'organizer_event_created';
}
