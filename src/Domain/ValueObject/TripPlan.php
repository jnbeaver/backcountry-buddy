<?php

namespace App\Domain\ValueObject;

use App\Domain\Entity\Dish;
use App\Domain\Entity\Meal;
use App\Domain\Entity\Trip;
use App\Domain\ValueObject\TripPlan\Chapter;
use App\Domain\ValueObject\TripPlan\MealPlan;
use App\Domain\ValueObject\TripPlan\RecipeHardCopy;
use Illuminate\Support\Collection;

readonly class TripPlan
{
    public function __construct(
        private Trip $trip
    ) {
    }

    /**
     * @return Chapter[]
     */
    public function getChapters(): array
    {
        return [
            new Chapter([new MealPlan($this->trip)]),
            ...(new Collection($this->trip->getMeals()))
                ->map(fn (Meal $meal) => $meal->getDishes())
                ->flatten(1)
                ->filter(fn (Dish $dish) => $dish->getRecipe() !== null)
                ->keyBy(fn (Dish $dish) => $dish->getId())
                ->values()
                ->sortBy(fn (Dish $dish) => $dish->getTitle())
                ->map(fn (Dish $dish) => new Chapter([new RecipeHardCopy($dish->getRecipe())]))
                ->all()
        ];
    }
}