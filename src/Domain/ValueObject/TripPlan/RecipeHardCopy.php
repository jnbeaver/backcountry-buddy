<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\RecipeImmutable;

readonly class RecipeHardCopy implements Section
{
    public function __construct(
        private RecipeImmutable $recipe
    ) {
    }

    public function getContent(): ?string
    {
        return sprintf(
            "%s\n%s\n%s\n%s\n%s\n%s\n",
            Markdown::header2($this->recipe->getTitle()),
            sprintf('From %s', Markdown::italic($this->recipe->getSource())),
            Markdown::header3('Ingredients'),
            Markdown::unorderedList($this->recipe->getIngredients()),
            Markdown::header3('Instructions'),
            Markdown::orderedList($this->recipe->getInstructions())
        );
    }
}
