<?php

namespace App\Domain\Enum;

trait EnumTrait
{
    public static function asChoice(): array
    {
        $cases = self::cases();

        return array_combine(
            array_column($cases, 'name'),
            $cases
        );
    }
}
