<?php

namespace App\Application\Handler;

use App\Application\Command\PlanTrip;
use App\Domain\Entity\TripImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\TripPlanService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PlanTripHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
        private TripPlanService $tripPlanService
    ) {
    }

    public function __invoke(PlanTrip $command): TripImmutable
    {
        $trip = $this->repositoryRegistry
            ->getTripRepository()
            ->findOrFail($command->id);

        $this->tripPlanService->create(
            $command->filename,
            $trip,
            $this->repositoryRegistry->getGearItemRepository()->findAll()
        );

        return $trip;
    }
}
