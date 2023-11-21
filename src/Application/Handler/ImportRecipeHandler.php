<?php

namespace App\Application\Handler;

use App\Application\Command\ImportRecipe;
use App\Domain\Entity\Recipe;
use App\Domain\Entity\RecipeImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\RecipeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ImportRecipeHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
        private RecipeService $recipeService
    ) {
    }

    public function __invoke(ImportRecipe $command): RecipeImmutable
    {
        $microdata = $this->recipeService->readMicrodata($command->url);
        $recipe = new Recipe($command->url, $microdata);

        $this->repositoryRegistry
            ->getRecipeRepository()
            ->save($recipe);

        return $recipe;
    }
}
