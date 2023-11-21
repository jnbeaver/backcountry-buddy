<?php

namespace App\Adapters\CLI;

use App\Application\Command\ImportRecipe;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsCommand(
    name: 'bb:recipe:import',
    description: 'Imports a recipe from a webpage that exposes Schema.org microdata.',
)]
class ImportRecipeCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'The webpage URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->write('Importing recipe...');

        $recipe = $this->commandBus
            ->dispatch(
                new ImportRecipe(
                    $input->getArgument('url')
                )
            )
            ->last(HandledStamp::class)
            ->getResult();

        $io->success("Recipe '{$recipe->getTitle()}' imported successfully!");

        return 0;
    }
}
