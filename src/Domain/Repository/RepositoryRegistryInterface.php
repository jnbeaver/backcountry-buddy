<?php

namespace App\Domain\Repository;

interface RepositoryRegistryInterface
{
    public function getRecipeRepository(): RecipeRepositoryInterface;
}
