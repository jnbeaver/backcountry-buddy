<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Dish;
use App\Domain\Entity\GearItem;
use App\Domain\Entity\Person;
use App\Domain\Entity\Recipe;
use App\Domain\Entity\Task;
use App\Domain\Entity\Trip;
use App\Domain\Repository\DishRepositoryInterface;
use App\Domain\Repository\GearItemRepositoryInterface;
use App\Domain\Repository\PersonRepositoryInterface;
use App\Domain\Repository\RecipeRepositoryInterface;
use App\Domain\Repository\RepositoryRegistryInterface;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\Repository\TripRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

readonly class RepositoryRegistry implements RepositoryRegistryInterface
{
    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
    }

    public function getDishRepository(): DishRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Dish::class);
    }

    public function getGearItemRepository(): GearItemRepositoryInterface
    {
        return $this->managerRegistry->getRepository(GearItem::class);
    }

    public function getPersonRepository(): PersonRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Person::class);
    }

    public function getRecipeRepository(): RecipeRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Recipe::class);
    }

    public function getTaskRepository(): TaskRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Task::class);
    }

    public function getTripRepository(): TripRepositoryInterface
    {
        return $this->managerRegistry->getRepository(Trip::class);
    }
}
