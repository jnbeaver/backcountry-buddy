<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateDish;
use App\Application\Command\CreateDishFromRecipe;
use App\Domain\Entity\DishImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'bb:dish:create',
    description: 'Creates a new dish, either manually or from an existing recipe.',
)]
class CreateDishCommand extends AbstractDoActionCommand
{
    protected function configure(): void
    {
        $this
            ->addOption('recipe', 'r', InputOption::VALUE_OPTIONAL, 'The recipe ID');
    }

    protected function initializeCommand(InputInterface $input, Style $io): CreateDish|CreateDishFromRecipe
    {
        $recipeId = $input->getOption('recipe');

        if ($recipeId !== null) {
            return new CreateDishFromRecipe(
                $recipeId,
                $io->askMany('Preparation Step')
            );
        }

        return new CreateDish(
            $io->askRequired('Title'),
            $io->askMany('Ingredient'),
            $io->askMany('Preparation Step')
        );
    }

    protected function getActionStartMessage(): string
    {
        return 'Creating dish...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var DishImmutable $result */
        return "Dish '{$result->getTitle()}' created successfully!";
    }
}
