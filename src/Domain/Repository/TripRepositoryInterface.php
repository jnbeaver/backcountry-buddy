<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Trip;
use App\Infrastructure\Exception\EntityNotFoundException;

interface TripRepositoryInterface
{
    /**
     * @param int $id
     * @return Trip
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): Trip;

    /**
     * @return string[]
     */
    public function findUniqueLocations(): array;

    public function save(Trip $trip): void;

    public function delete(Trip $trip): void;
}
