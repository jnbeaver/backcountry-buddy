<?php

namespace App\Application\Command;

readonly class CreateDishFromRecipe
{
    /**
     * @param int $recipeId
     * @param string[] $prep
     */
    public function __construct(
        public int $recipeId,
        public array $prep
    ) {
    }
}
