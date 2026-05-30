<?php

namespace App\Enum;

enum ReportStatus: string
{
    case PENDING = 'pending';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';
}
