<?php

namespace App\Application\Command;

readonly class CreateDishFromRecipe
{
    /**
     * @param int $recipeId
     * @param string[] $prep
     * @param int[] $gearItemIds
     */
    public function __construct(
        public int $recipeId,
        public array $prep,
        public array $gearItemIds
    ) {
    }
}
