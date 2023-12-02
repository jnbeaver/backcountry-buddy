<?php

namespace App\Domain\Enum;

enum AssigneeType: string
{
    use EnumTrait;

    case Adults = 'adults';

    case Children = 'children';

    case All = 'all';

    case Individual = 'individual';
}
