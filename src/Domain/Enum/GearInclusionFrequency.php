<?php

namespace App\Domain\Enum;

enum GearInclusionFrequency: string
{
    use EnumTrait {
        asChoice as traitAsChoice;
    }

    case Always = 'always';

    case Sometimes = 'sometimes';

    case OnlyWhenRequiredForMealPrep = 'for_meal_prep';

    public static function asChoice(): array
    {
        $map = self::traitAsChoice();

        $key = self::OnlyWhenRequiredForMealPrep->name;
        $map['Only When Required for Meal Prep'] = $map[$key];
        unset($map[$key]);

        return $map;
    }
}
