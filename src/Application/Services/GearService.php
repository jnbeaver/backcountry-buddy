<?php

namespace App\Application\Services;

use App\Domain\Entity\GearItemImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;

readonly class GearService
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
    ) {
    }

    /**
     * @return GearItemImmutable[]
     */
    public function getAll(): array
    {
        return $this->repositoryRegistry->getGearItemRepository()->findAll();
    }
}
