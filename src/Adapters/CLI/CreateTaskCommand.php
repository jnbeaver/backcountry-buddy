<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateTask;
use App\Application\Services\TripCriteriaService;
use App\Domain\Entity\TaskImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bb:task:create',
    description: 'Creates a new task.',
)]
class CreateTaskCommand extends AbstractDoActionCommand
{
    public function __construct(
        MessageBusInterface $commandBus,
        private readonly TripCriteriaService $tripCriteriaService
    ) {
        parent::__construct($commandBus);
    }

    protected function initializeCommand(InputInterface $input, Style $io): CreateTask
    {
        $title = $io->askRequired('Title');
        $inclusionFrequency = $io->choice('Include this Task on a Trip', ['Always', 'Sometimes']);

        $tripCriteriaExpression = null;

        if ($inclusionFrequency === 'Sometimes') {
            $tripCriteriaExpression = $io->askTripCriteriaExpression(
                'Trip Criteria Expression',
                $this->tripCriteriaService
            );
        }

        return new CreateTask($title, $tripCriteriaExpression);
    }

    protected function getActionStartMessage(): string
    {
        return 'Creating task...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        return "Task created successfully!";
    }
}
