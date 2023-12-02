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

    /**
     * @return string[]
     */
    public function getPrep(): array;

    /**
     * @return GearItemImmutable[]
     */
    public function getRequiredGear(): array;
}
