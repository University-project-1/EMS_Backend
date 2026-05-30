<?php

namespace App\Enum;

enum SystemUserRole: string
{
    case ADMIN = 'admin';
    case EXHIBITOR = 'exhibitor';
}
