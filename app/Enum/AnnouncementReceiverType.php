<?php

namespace App\Enum;

enum AnnouncementReceiverType: string
{
    case Visitor = 'visitor';
    case Exhibitor = 'exhibitor';
    case All = 'all';
}
