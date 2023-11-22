<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Dish;
use App\Domain\Entity\Person;
use App\Domain\Entity\Recipe;
use App\Domain\Repository\DishRepositoryInterface;
use App\Domain\Repository\PersonRepositoryInterface;
use App\Domain\Repository\RecipeRepositoryInterface;
use App\Domain\Repository\RepositoryRegistryInterface;
use Doctrine\Persistence\ManagerRegistry;

class RepositoryRegistry implements RepositoryRegistryInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    public function getDishRepository(): DishRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Dish::class);
    }

    public function getPersonRepository(): PersonRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Person::class);
    }

    public function getRecipeRepository(): RecipeRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Recipe::class);
    }
}
