<?php

namespace App\Domain\Entity;

use App\Domain\Enum\TripType;
use DateTime;

interface TripImmutable
{
    public function getId(): int;

    public function getType(): TripType;

    public function getLocation(): string;

    public function getStartDate(): DateTime;

    public function getEndDate(): DateTime;

    public function getLowTemp(): int;

    public function getHighTemp(): int;

    /**
     * @return PersonImmutable[]
     */
    public function getAttendees(): array;

    /**
     * @return Meal[]
     */
    public function getMeals(): array;
}