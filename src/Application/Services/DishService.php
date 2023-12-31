<?php

namespace App\Application\Services;

use App\Domain\Entity\DishImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;

readonly class DishService
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
    ) {
    }

    /**
     * @return DishImmutable[]
     */
    public function getAll(): array
    {
        return $this->repositoryRegistry->getDishRepository()->findAll();
    }
}
