<?php

namespace App\Enum;

enum HallType: string
{
    case EXHIBITION = 'exhibition';
    case RESTAURANT = 'restaurant';
    case MOSQUE = 'mosque';
    case BATHROOM = 'bathroom';
    case PARKING = 'parking';

}
