<?php

namespace App\Application\Handler;

use App\Application\Command\CreateDishFromRecipe;
use App\Domain\Entity\Dish;
use App\Domain\Entity\DishImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateDishFromRecipeHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    public function __invoke(CreateDishFromRecipe $command): DishImmutable
    {
        $recipe = $this->repositoryRegistry
            ->getRecipeRepository()
            ->findOrFail($command->recipeId);

        $dish = Dish::fromRecipe($recipe);

        $this->repositoryRegistry->getDishRepository()->save($dish);

        return $dish;
    }
}
