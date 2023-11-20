<?php

namespace App\Adapters\CLI;

use App\Application\Command\CommandInterface;
use App\Application\Command\ImportRecipe;
use App\Domain\Entity\RecipeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bb:recipe:import',
    description: 'Imports a recipe from a webpage that exposes Schema.org microdata.',
    hidden: false,
)]
class ImportRecipeCommand extends Command implements CommandInterface
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

        /** @var RecipeImmutable $recipe */
        $recipe = $this->commandBus->dispatch(
            new ImportRecipe(
                $input->getArgument('url')
            )
        );

        $io->write('Successfully imported recipe:');
        $io->newLine();
        $io->success($recipe->getTitle());

        return 0;
    }
}
