<?php

namespace App\Adapters\CLI;

use App\Application\Command\RefreshRecipe;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsCommand(
    name: 'bb:recipe:refresh',
    description: 'Refreshes a recipe from its parent webpage.',
)]
class RefreshRecipeCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('idOrUrl', InputArgument::REQUIRED, 'The recipe ID or parent webpage URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        $io->writeln('Refreshing recipe...');

        $recipe = $this->commandBus
            ->dispatch(
                new RefreshRecipe(
                    $input->getArgument('idOrUrl')
                )
            )
            ->last(HandledStamp::class)
            ->getResult();

        $io->success("Recipe '{$recipe->getTitle()}' refreshed successfully!");

        return 0;
    }
}
