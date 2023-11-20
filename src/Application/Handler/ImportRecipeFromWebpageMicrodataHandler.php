<?php

namespace App\Application\Handler;

use App\Application\Command\ImportRecipeFromWebpageMicrodata;
use App\Domain\Entity\Recipe;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Services\RecipeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class ImportRecipeFromWebpageMicrodataHandler
{
    public function __construct(
        private readonly RepositoryRegistryInterface $repositoryRegistry,
        private readonly RecipeService $recipeService
    ) {
    }

    public function __invoke(ImportRecipeFromWebpageMicrodata $command): void
    {
        $microdata = $this->recipeService->readMicrodata($command->getUrl());

        $this->repositoryRegistry
            ->getRecipeRepository()
            ->save(
                Recipe::fromWebpageMicrodata($command->getUrl(), $microdata)
            );
    }
}
