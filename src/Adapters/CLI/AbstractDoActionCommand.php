<?php

namespace App\Adapters\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

abstract class AbstractDoActionCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        $command = $this->initializeCommand($input, $io);

        $io->writeln($this->getActionStartMessage());

        $result = $this->commandBus
            ->dispatch($command)
            ->last(HandledStamp::class)
            ->getResult();

        $io->writeln($this->getActionSuccessMessage($result));

        return 0;
    }

    abstract protected function initializeCommand(InputInterface $input, Style $io): object;

    abstract protected function getActionStartMessage(): string;

    abstract protected function getActionSuccessMessage(object $result): string;
}
