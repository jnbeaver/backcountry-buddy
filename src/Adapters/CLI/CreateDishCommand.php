<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateDish;
use App\Application\Command\CreateDishFromRecipe;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsCommand(
    name: 'bb:dish:create',
    description: 'Creates a new dish, either manually or from an existing recipe.',
)]
class CreateDishCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('recipe', 'r', InputOption::VALUE_OPTIONAL, 'The recipe ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new Style($input, $output);

        $recipeId = $input->getOption('recipe');

        if ($recipeId !== null) {
            $command = new CreateDishFromRecipe(
                $recipeId,
                $io->askMany('Preparation Step')
            );
        } else {
            $command = new CreateDish(
                $io->ask('Title'),
                $io->askMany('Ingredient'),
                $io->askMany('Preparation Step')
            );
        }

        $io->writeln('Creating dish...');

        $dish = $this->commandBus
            ->dispatch($command)
            ->last(HandledStamp::class)
            ->getResult();

        $io->success("Dish '{$dish->getTitle()}' created successfully!");

        return 0;
    }
}
