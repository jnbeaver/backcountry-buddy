<?php

namespace App\Domain\ValueObject\TripPlan;

interface Section
{
    public function getContent(): ?string;
}
