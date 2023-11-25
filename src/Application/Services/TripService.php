<?php

namespace App\Application\Services;

use App\Domain\Repository\RepositoryRegistryInterface;

readonly class TripService
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    public function getExistingTripLocations(): array
    {
        return $this->repositoryRegistry->getTripRepository()->findUniqueLocations();
    }
}