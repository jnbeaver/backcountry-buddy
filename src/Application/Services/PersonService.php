<?php

namespace App\Application\Services;

use App\Domain\Entity\PersonImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;

readonly class PersonService
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    /**
     * @return PersonImmutable[]
     */
    public function getAll(): array
    {
        return $this->repositoryRegistry->getPersonRepository()->findAll();
    }
}
