<?php

namespace App\Enum;

enum FacilityType: string
{
    case BATHROOM = 'bathroom';
    case MOSQUE = 'mosque';
    case PARKING = 'parking';
    case HVAC = 'hvac';
    case PRESS = 'press';
    case VIP_LOUNGE = 'vip_lounge';
    case ENTRANCE_EXIT = 'entrance_exit';
    case GOODS_ENTRANCE = 'goods_entrance';
    case EMERGENCY_EXIT = 'emergency_exit';
    case ENTRANCE = 'entrance';
}
