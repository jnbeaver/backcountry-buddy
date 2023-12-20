<?php

namespace App\Application\Handler;

use App\Application\Command\DeleteTrip;
use App\Domain\Repository\RepositoryRegistryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteTripHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    public function __invoke(DeleteTrip $command): void
    {
        $repository = $this->repositoryRegistry->getTripRepository();

        $repository->delete($repository->findOrFail($command->id));
    }
}
