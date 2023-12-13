<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\Dish;
use App\Domain\Entity\Meal;
use App\Domain\Entity\TripImmutable;
use App\Domain\Enum\MealType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class MealPlan implements Section
{
    public function __construct(
        private TripImmutable $trip
    ) {
    }

    public function getContent(): ?string
    {
        $meals = new Collection($this->trip->getMeals());

        if ($meals->isEmpty()) {
            return null;
        }

        $dateFormat = 'D, n/j';

        if (!Carbon::instance($this->trip->getStartDate())->isSameYear($this->trip->getEndDate())) {
            $dateFormat .= '/y';
        }

        $dates = (new Collection($this->trip->getDays()))
            ->map(fn (Carbon $date) => $date->format($dateFormat));

        $mealTypes = (new Collection(MealType::cases()))
            ->map(fn (MealType $mealType) => $mealType->name)
            ->intersect(
                $meals
                    ->groupBy(fn (Meal $meal) => $meal->getType()->name)
                    ->filter(fn (Collection $meals) => $meals->isNotEmpty())
                    ->keys()
            )
            ->values();

        $mealsByDateAndType = $meals
            ->groupBy(fn (Meal $meal) => $meal->getDate()->format($dateFormat))
            ->map(function (Collection $dateMeals) use ($mealTypes) {
                return $mealTypes
                    ->flip()
                    ->map(fn (string $mealType) => '')
                    ->merge(
                        $dateMeals
                            ->keyBy(fn (Meal $meal) => $meal->getType()->name)
                            ->map(function (Meal $meal) {
                                return (new Collection($meal->getDishes()))
                                    ->map(fn (Dish $dish) => $dish->getTitle())
                                    ->join("\n");
                            })
                    );
            });

        return sprintf(
            "%s\n%s",
            Markdown::header3('Meal Plan'),
            Markdown::table([
                array_merge(['Date'], $mealTypes->all()),
                ...$dates
                    ->flip()
                    ->map(fn (string $date) => new Collection())
                    ->merge($mealsByDateAndType)
                    ->filter(fn (Collection $meals) => $meals->isNotEmpty())
                    ->map(function (Collection $meals, string $date) {
                        return $meals
                            ->prepend($date)
                            ->values()
                            ->all();
                    })
                    ->values()
                    ->all()
            ])
        );
    }
}
