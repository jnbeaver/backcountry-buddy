<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Recipe;
use App\Domain\Repository\RecipeRepositoryInterface;
use App\Domain\Repository\RepositoryRegistryInterface;
use Doctrine\Persistence\ManagerRegistry;

class RepositoryRegistry implements RepositoryRegistryInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    public function getRecipeRepository(): RecipeRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Recipe::class);
    }
}
