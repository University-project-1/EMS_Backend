<?php

namespace App\Enum;

enum SystemUserType: string
{
    case ADMIN = 'admin';
    case EXHIBITOR = 'exhibitor';
}
