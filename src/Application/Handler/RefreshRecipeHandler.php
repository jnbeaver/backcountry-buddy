<?php

namespace App\Application\Handler;

use App\Application\Command\RefreshRecipe;
use App\Domain\Entity\RecipeImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\RecipeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class RefreshRecipeHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry,
        private RecipeService $recipeService
    ) {
    }

    public function __invoke(RefreshRecipe $command): RecipeImmutable
    {
        $recipeRepository = $this->repositoryRegistry->getRecipeRepository();
        $id = $this->parseId($command->idOrUrl);

        if ($id !== null) {
            $recipe = $recipeRepository->findOrFail($id);
        } else {
            $recipe = $recipeRepository->findOrFailByUrl($command->idOrUrl);
        }

        $microdata = $this->recipeService->readMicrodata($recipe->getUrl());
        $recipe->refresh($microdata);

        $recipeRepository->save($recipe);

        return $recipe;
    }

    public function parseId(string $idOrUrl): ?int
    {
        $id = filter_var($idOrUrl, FILTER_VALIDATE_INT);

        if ($id === false) {
            return null;
        }

        return $id;
    }
}
