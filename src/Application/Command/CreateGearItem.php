<?php

namespace App\Application\Command;

use App\Domain\Enum\AssigneeType;
use App\Domain\Enum\GearInclusionFrequency;

readonly class CreateGearItem
{
    public function __construct(
        public string $name,
        public GearInclusionFrequency $inclusionFrequency,
        public ?string $tripCriteriaExpression,
        public ?AssigneeType $assigneeType,
        public ?int $assigneeId
    ) {
    }
}
