<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateDish;
use App\Application\Command\CreateDishFromRecipe;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
        $io = new SymfonyStyle($input, $output);

        $recipeId = $input->getOption('recipe');

        if ($recipeId !== null) {
            $command = new CreateDishFromRecipe(
                $recipeId,
                $this->askPrep($io)
            );
        } else {
            $command = new CreateDish(
                $this->askTitle($io),
                $this->askIngredients($io),
                $this->askPrep($io)
            );
        }

        $io->write('Creating dish...');

        $dish = $this->commandBus
            ->dispatch($command)
            ->last(HandledStamp::class)
            ->getResult();

        $io->success("Dish '{$dish->getTitle()}' created successfully!");

        return 0;
    }

    private function askTitle(SymfonyStyle $io): string
    {
        return $io->ask('Title', null, function ($title) {
            if (empty($title)) {
                throw new RuntimeException('The title cannot be empty.');
            }

            return $title;
        });
    }

    /**
     * @param SymfonyStyle $io
     * @return string[]
     */
    private function askIngredients(SymfonyStyle $io): array
    {
        return $this->askStringArray('Ingredient', $io);
    }

    /**
     * @param SymfonyStyle $io
     * @return string[]
     */
    private function askPrep(SymfonyStyle $io): array
    {
        return $this->askStringArray('Preparation Step', $io);
    }

    /**
     * @param string $name
     * @param SymfonyStyle $io
     * @return string[]
     */
    private function askStringArray(string $name, SymfonyStyle $io): array
    {
        $result = [];

        do {
            $item = $io->ask(
                sprintf(
                    '%s #%s%s',
                    $name,
                    $num = count($result) + 1,
                    $num === 1 ? ' (leave empty to finish)' : ''
                )
            );
        } while (!empty($item) && array_push($result, $item));

        return $result;
    }
}