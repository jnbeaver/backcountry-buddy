<?php

namespace App\Domain\Entity;

interface DishImmutable
{
    public function getId(): int;

    public function getRecipe(): ?RecipeImmutable;

    public function getTitle(): string;

    /**
     * @return string[]
     */
    public function getIngredients(): array;
}
