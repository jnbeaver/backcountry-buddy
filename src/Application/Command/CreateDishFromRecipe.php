<?php

namespace App\Application\Command;

readonly class CreateDishFromRecipe
{
    public function __construct(
        public int $recipeId
    ) {
    }
}
