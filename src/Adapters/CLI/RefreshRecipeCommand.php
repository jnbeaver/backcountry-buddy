<?php

namespace App\Adapters\CLI;

use App\Application\Command\CommandInterface;
use App\Application\Command\RefreshRecipe;
use App\Domain\Entity\RecipeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bb:recipe:refresh',
    description: 'Refreshes a recipe from its parent webpage.',
    hidden: false,
)]
class RefreshRecipeCommand extends Command implements CommandInterface
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
        $io = new SymfonyStyle($input, $output);

        /** @var RecipeImmutable $recipe */
        $recipe = $this->commandBus->dispatch(
            new RefreshRecipe(
                $input->getArgument('idOrUrl')
            )
        );

        $io->write('Successfully refreshed recipe:');
        $io->newLine();
        $io->success($recipe->getTitle());

        return 0;
    }
}
