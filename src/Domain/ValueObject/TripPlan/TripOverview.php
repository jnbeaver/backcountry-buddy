<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\TripImmutable;

readonly class TripOverview implements Section
{
    private const DATE_FORMAT = 'l, F jS, Y';

    public function __construct(
        private TripImmutable $trip
    ) {
    }

    public function getContent(): ?string
    {
        return sprintf(
            "%s\n%s - %s\n",
            Markdown::header1("Trip to {$this->trip->getLocation()}"),
            $this->trip->getStartDate()->format(self::DATE_FORMAT),
            $this->trip->getEndDate()->format(self::DATE_FORMAT)
        );
    }
}
