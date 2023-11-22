<?php

namespace App\Adapters\CLI;

use App\Application\Command\RefreshRecipe;
use App\Domain\Entity\RecipeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(
    name: 'bb:recipe:refresh',
    description: 'Refreshes a recipe from its parent webpage.',
)]
class RefreshRecipeCommand extends AbstractDoActionCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('idOrUrl', InputArgument::REQUIRED, 'The recipe ID or parent webpage URL');
    }

    protected function initializeCommand(InputInterface $input, Style $io): RefreshRecipe
    {
        return new RefreshRecipe($input->getArgument('idOrUrl'));
    }

    protected function getActionStartMessage(): string
    {
        return 'Refreshing recipe...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var RecipeImmutable $result */
        return "Recipe '{$result->getTitle()}' refreshed successfully!";
    }
}
