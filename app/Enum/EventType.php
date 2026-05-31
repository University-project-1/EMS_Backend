<?php

namespace App\Enum;

enum EventType: string
{
    case CONFERENCE = 'conference';
    case WORKSHOP = 'workshop';
    case LECTURE = 'lecture';
    case OTHER = 'other';
}
