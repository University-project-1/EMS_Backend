<?php

namespace App\Enum;

enum EventType: string
{
    case CONFERENCE = 'conference';
    case WORKSHOP = 'workshop';
    case lECTURE = 'lecture';
    case OTHER = 'other';
}
