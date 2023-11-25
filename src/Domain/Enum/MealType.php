<?php

namespace App\Domain\Enum;

enum MealType: string
{
    use EnumTrait;

    case Breakfast = 'breakfast';

    case Lunch = 'lunch';

    case Dinner = 'dinner';
}
