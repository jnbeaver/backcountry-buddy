<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\Assignee;
use App\Domain\ValueObject\GearInclusionStrategy;

interface GearItemImmutable
{
    public function getId(): int;

    public function getName(): string;

    public function getInclusionStrategy(): GearInclusionStrategy;

    public function getAssignee(): ?Assignee;
}
