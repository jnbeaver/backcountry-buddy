<?php

namespace App\Application\Handler;

use App\Application\Command\CreateGearItem;
use App\Domain\Entity\GearItem;
use App\Domain\Entity\GearItemImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\TripCriteriaService;
use App\Domain\ValueObject\Assignee;
use App\Domain\ValueObject\GearInclusionStrategy;
use App\Domain\ValueObject\TripCriteria;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateGearItemHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
        private TripCriteriaService $tripCriteriaService
    ) {
    }

    public function __invoke(CreateGearItem $command): GearItemImmutable
    {
        $assignee = null;

        if ($command->assigneeType !== null) {
            $assignee = new Assignee($command->assigneeType, $command->assigneeId);
        }

        $gearItem = new GearItem(
            $command->name,
            new GearInclusionStrategy(
                $command->inclusionFrequency,
                $this->tripCriteriaService->create($command->tripCriteriaExpression)
            ),
            $assignee
        );

        $this->repositoryRegistry->getGearItemRepository()->save($gearItem);

        return $gearItem;
    }
}
