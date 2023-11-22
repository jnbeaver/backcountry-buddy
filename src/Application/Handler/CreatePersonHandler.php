<?php

namespace App\Application\Handler;

use App\Application\Command\CreatePerson;
use App\Domain\Entity\Person;
use App\Domain\Entity\PersonImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreatePersonHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    public function __invoke(CreatePerson $command): PersonImmutable
    {
        $person = new Person($command->name, $command->isChild);

        $this->repositoryRegistry
            ->getPersonRepository()
            ->save($person);

        return $person;
    }
}
