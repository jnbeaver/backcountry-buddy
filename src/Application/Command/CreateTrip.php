<?php

namespace App\Application\Command;

use App\Domain\Enum\TripType;
use DateTime;

readonly class CreateTrip
{
    /**
     * @param TripType $type
     * @param string $location
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int[] $attendeeIds
     * @param int $lowTemp
     * @param int $highTemp
     * @param array<string, array<string, int[]>> $mealPlan
     */
    public function __construct(
        public TripType $type,
        public string $location,
        public DateTime $startDate,
        public DateTime $endDate,
        public array $attendeeIds,
        public int $lowTemp,
        public int $highTemp,
        public array $mealPlan
    ) {
    }
}
