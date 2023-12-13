<?php

namespace App\Domain\ValueObject;

use App\Domain\Entity\Dish;
use App\Domain\Entity\GearItemImmutable;
use App\Domain\Entity\Meal;
use App\Domain\Entity\TripImmutable;
use App\Domain\Services\TripCriteriaService;
use App\Domain\ValueObject\TripPlan\Chapter;
use App\Domain\ValueObject\TripPlan\GearList;
use App\Domain\ValueObject\TripPlan\MealPlan;
use App\Domain\ValueObject\TripPlan\RecipeHardCopy;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

readonly class TripPlan
{
    /**
     * @param TripImmutable $trip
     * @param GearItemImmutable[] $gear
     * @param TripCriteriaService $tripCriteriaService
     */
    public function __construct(
        private TripImmutable $trip,
        private array $gear,
        private TripCriteriaService $tripCriteriaService
    ) {
        Assert::allIsInstanceOf($gear, GearItemImmutable::class);
    }

    /**
     * @return Chapter[]
     */
    public function getChapters(): array
    {
        return [
            new Chapter([
                new GearList($this->trip, $this->gear, $this->tripCriteriaService),
            ]),
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
