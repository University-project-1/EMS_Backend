<?php

namespace App\Enum;

enum AnnouncementNotificationAction: string
{
    case Created = 'created';
    case Updated = 'updated';
}
