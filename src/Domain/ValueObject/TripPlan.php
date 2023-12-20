<?php

namespace App\Domain\ValueObject;

use App\Domain\Entity\Dish;
use App\Domain\Entity\GearItemImmutable;
use App\Domain\Entity\Meal;
use App\Domain\Entity\TaskImmutable;
use App\Domain\Entity\TripImmutable;
use App\Domain\Services\TripCriteriaService;
use App\Domain\ValueObject\TripPlan\Chapter;
use App\Domain\ValueObject\TripPlan\GearList;
use App\Domain\ValueObject\TripPlan\MealPlan;
use App\Domain\ValueObject\TripPlan\MealPrep;
use App\Domain\ValueObject\TripPlan\RecipeHardCopy;
use App\Domain\ValueObject\TripPlan\TaskList;
use App\Domain\ValueObject\TripPlan\TripOverview;
use App\Domain\ValueObject\TripPlan\WeatherForecast;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

readonly class TripPlan
{
    /**
     * @param TripImmutable $trip
     * @param GearItemImmutable[] $gear
     * @param TaskImmutable[] $tasks
     * @param TripCriteriaService $tripCriteriaService
     */
    public function __construct(
        private TripImmutable $trip,
        private array $gear,
        private array $tasks,
        private TripCriteriaService $tripCriteriaService
    ) {
        Assert::allIsInstanceOf($gear, GearItemImmutable::class);
        Assert::allIsInstanceOf($tasks, TaskImmutable::class);
    }

    /**
     * @return Chapter[]
     */
    public function getChapters(): array
    {
        return [
            new Chapter([
                new TripOverview($this->trip),
                new WeatherForecast($this->trip),
                new GearList($this->trip, $this->gear, $this->tripCriteriaService),
                new MealPrep($this->trip),
                new TaskList($this->trip, $this->tasks, $this->tripCriteriaService),
            ]),
            new Chapter([
                new WeatherForecast($this->trip),
                new MealPlan($this->trip),
            ]),
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
