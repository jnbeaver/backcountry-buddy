<?php

namespace App\Domain\Enum;

enum TripType: string
{
    use EnumTrait;

    case Backpacking = 'backpacking';

    case RV = 'rv';

    case Tent = 'tent';
}
