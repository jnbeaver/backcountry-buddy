<?php

namespace App\Domain\Entity;

interface RecipeImmutable
{
    public function getId(): int;

    public function getUrl(): string;

    public function getSource(): string;

    public function getTitle(): string;

    /**
     * @return string[]
     */
    public function getInstructions(): array;

    /**
     * @return string[]
     */
    public function getIngredients(): array;
}