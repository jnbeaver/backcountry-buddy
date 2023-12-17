<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\TripCriteria;

interface TaskImmutable
{
    public function getId(): int;

    public function getTitle(): string;

    public function getTripCriteria(): ?TripCriteria;
}
