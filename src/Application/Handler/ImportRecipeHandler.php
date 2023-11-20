<?php

namespace App\Application\Handler;

use App\Application\Command\ImportRecipe;
use App\Domain\Entity\Recipe;
use App\Domain\Entity\RecipeImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\RecipeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportRecipeHandler
{
    public function __construct(
        private readonly RepositoryRegistryInterface $repositoryRegistry,
        private readonly RecipeService $recipeService
    ) {
    }

    public function __invoke(ImportRecipe $command): RecipeImmutable
    {
        $microdata = $this->recipeService->readMicrodata($command->getUrl());
        $recipe = new Recipe($command->getUrl(), $microdata);

        $this->repositoryRegistry
            ->getRecipeRepository()
            ->save($recipe);

        return $recipe;
    }
}
