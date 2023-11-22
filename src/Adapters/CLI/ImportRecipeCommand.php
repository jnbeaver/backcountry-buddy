<?php

namespace App\Adapters\CLI;

use App\Application\Command\ImportRecipe;
use App\Domain\Entity\RecipeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(
    name: 'bb:recipe:import',
    description: 'Imports a recipe from a webpage that exposes Schema.org microdata.',
)]
class ImportRecipeCommand extends AbstractDoActionCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'The webpage URL');
    }

    protected function initializeCommand(InputInterface $input, Style $io): ImportRecipe
    {
        return new ImportRecipe($input->getArgument('url'));
    }

    protected function getActionStartMessage(): string
    {
        return 'Importing recipe...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var RecipeImmutable $result */
        return "Recipe '{$result->getTitle()}' imported successfully!";
    }
}
