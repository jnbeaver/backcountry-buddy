<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\DishImmutable;
use App\Domain\Entity\Meal;
use App\Domain\Entity\TripImmutable;
use Illuminate\Support\Collection;

readonly class MealPrep implements Section
{
    public function __construct(
        private TripImmutable $trip
    ) {
    }

    public function getContent(): ?string
    {
        $dishes = (new Collection($this->trip->getMeals()))
            ->map(fn (Meal $meal) => $meal->getDishes())
            ->flatten()
            ->filter(fn (DishImmutable $dish) => !empty($dish->getPrep()) || !empty($dish->getIngredients()))
            ->keyBy(fn (DishImmutable $dish) => $dish->getTitle())
            ->sortBy(fn (DishImmutable $dish) => $dish->getTitle());

        if ($dishes->isEmpty()) {
            return null;
        }

        return sprintf(
            "%s\n%s",
            Markdown::header2('Meal Prep'),
            $dishes
                ->map(
                    function (DishImmutable $dish, string $title) {
                        $content = '';

                        if (!empty($dish->getIngredients())) {
                            $content .= sprintf(
                                "%s\n%s\n",
                                Markdown::header4('Ingredients'),
                                Markdown::unorderedList($dish->getIngredients())
                            );
                        }

                        if (!empty($dish->getPrep())) {
                            $content .= sprintf(
                                "%s\n%s\n",
                                Markdown::header4('Tasks'),
                                Markdown::tasklist($dish->getPrep())
                            );
                        }

                        return sprintf(
                            "%s\n%s",
                            Markdown::header3($title),
                            $content
                        );
                    }
                )
                ->join('')
        );
    }
}
