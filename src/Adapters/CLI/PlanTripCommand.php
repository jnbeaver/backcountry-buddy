<?php

namespace App\Adapters\CLI;

use App\Application\Command\PlanTrip;
use App\Domain\Entity\TripImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(
    name: 'bb:trip:plan',
    description: 'Creates a plan for an existing trip.',
)]
class PlanTripCommand extends AbstractDoActionCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The trip ID')
            ->addArgument('filename', InputArgument::REQUIRED, 'The trip plan document filename');
    }

    protected function initializeCommand(InputInterface $input, Style $io): object
    {
        return new PlanTrip(
            (int) $input->getArgument('id'),
            $input->getArgument('filename')
        );
    }

    protected function getActionStartMessage(): string
    {
        return 'Planning trip...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var TripImmutable $result */
        return sprintf(
            "Trip to %s from %s to %s planned successfully!",
            $result->getLocation(),
            $result->getStartDate()->format('n/j/y'),
            $result->getEndDate()->format('n/j/y')
        );
    }
}
