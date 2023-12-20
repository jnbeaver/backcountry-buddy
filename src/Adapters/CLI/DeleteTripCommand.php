<?php

namespace App\Adapters\CLI;

use App\Application\Command\DeleteTrip;
use App\Application\Command\PlanTrip;
use App\Domain\Entity\TripImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(
    name: 'bb:trip:delete',
    description: 'Deletes a trip.',
)]
class DeleteTripCommand extends AbstractDoActionCommand
{
    private int $id;

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The trip ID');
    }

    protected function initializeCommand(InputInterface $input, Style $io): object
    {
        $this->id = (int) $input->getArgument('id');

        return new DeleteTrip($this->id);
    }

    protected function getActionStartMessage(): string
    {
        return 'Deleting trip...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var TripImmutable $result */
        return sprintf(
            "Successfully deleted trip '%s'!",
            $this->id
        );
    }
}
