<?php

namespace App\Domain\Repository;

interface RepositoryRegistryInterface
{
    public function getDishRepository(): DishRepositoryInterface;

    public function getGearItemRepository(): GearItemRepositoryInterface;

    public function getPersonRepository(): PersonRepositoryInterface;

    public function getRecipeRepository(): RecipeRepositoryInterface;

    public function getTripRepository(): TripRepositoryInterface;
}
