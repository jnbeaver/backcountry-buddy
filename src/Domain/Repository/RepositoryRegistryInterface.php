<?php

namespace App\Domain\Repository;

interface RepositoryRegistryInterface
{
    public function getDishRepository(): DishRepositoryInterface;

    public function getRecipeRepository(): RecipeRepositoryInterface;
}
