<?php

namespace App\Enum;

enum BusinessSectors: string{
    case AGRICULTURE = 'agriculture';

    case ARTS = 'arts';
    case CONSTRUCTION = 'construction';
    case CULTURE = 'culture';

    case EDUCATION = 'education';
    case RESEARCH = 'research';

    case ENERGY = 'energy';
    case ENGINEERING = 'engineering';
    case ENVIRONMENT = 'environment';

    case FASHION = 'fashion';
    case FINANCE = 'finance';
    case FOOD_AND_BEVERAGE = 'food_and_beverage';

    case GOVERNMENT = 'government';

    case HEALTHCARE = 'healthcare';
    case HUMANITARIAN = 'humanitarian';

    case INDUSTRIAL = 'industrial';
    case INFORMATION_TECHNOLOGY = 'information_technology';

    case MANUFACTURING = 'manufacturing';
    case MEDIA = 'media';

    case NON_PROFIT = 'non_profit';

    case COMMERCE = 'commerce';

    case SOCIAL_DEVELOPMENT = 'social_development';
    case SPORTS = 'sports';

    case TECH = 'tech';
    case TELECOMMUNICATIONS = 'telecommunications';
    case TOURISM = 'tourism';
    case TRANSPORTATION_LOGISTICS = 'transportation_logistics';

    case ENTERTAINMENT = 'entertainment';

    case OTHER = 'other';
}
