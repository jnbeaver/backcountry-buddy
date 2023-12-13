<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\TripImmutable;

class WeatherForecast implements Section
{
    private const DATE_FORMAT = 'D, n/j';

    public function __construct(
        private readonly TripImmutable $trip
    ) {
    }

    public function getContent(): ?string
    {
        return sprintf(
            "%s\n%s\n",
            Markdown::header2('Weather Forecast'),
            Markdown::table(
                ['Date', 'Low Temp', 'High Temp'],
                [
                    [
                        sprintf(
                            '%s - %s',
                            $this->trip->getStartDate()->format(self::DATE_FORMAT),
                            $this->trip->getEndDate()->format(self::DATE_FORMAT)
                        ),
                        "{$this->trip->getLowTemp()}°",
                        "{$this->trip->getHighTemp()}°",
                    ]
                ],
            )
        );
    }
}
