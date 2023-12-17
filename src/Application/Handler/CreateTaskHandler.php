<?php

namespace App\Application\Handler;

use App\Application\Command\CreateTask;
use App\Domain\Entity\Task;
use App\Domain\Entity\TaskImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\TripCriteriaService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateTaskHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
        private TripCriteriaService $tripCriteriaService
    ) {
    }

    public function __invoke(CreateTask $command): TaskImmutable
    {
        $tripCriteria = null;

        if ($command->tripCriteriaExpression) {
            $tripCriteria = $this->tripCriteriaService->create($command->tripCriteriaExpression);
        }

        $task = new Task($command->title, $tripCriteria);

        $this->repositoryRegistry->getTaskRepository()->save($task);

        return $task;
    }
}
