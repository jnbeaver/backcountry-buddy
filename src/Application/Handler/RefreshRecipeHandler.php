<?php

namespace App\Application\Handler;

use App\Application\Command\RefreshRecipe;
use App\Domain\Entity\RecipeImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\RecipeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RefreshRecipeHandler
{
    public function __construct(
        private readonly RepositoryRegistryInterface $repositoryRegistry,
        private readonly RecipeService $recipeService
    ) {
    }

    public function __invoke(RefreshRecipe $command): RecipeImmutable
    {
        $recipeRepository = $this->repositoryRegistry->getRecipeRepository();

        if (is_int($command->getIdOrUrl())) {
            $recipe = $recipeRepository->findOrFail((int) $command->getIdOrUrl());
        } else {
            $recipe = $recipeRepository->findOrFailByUrl($command->getIdOrUrl());
        }

        $microdata = $this->recipeService->readMicrodata($recipe->getUrl());
        $recipe->refresh($microdata);

        $recipeRepository->save($recipe);

        return $recipe;
    }
}
