<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Recipe;
use App\Infrastructure\Exception\EntityNotFoundException;

interface RecipeRepositoryInterface
{
    /**
     * @param int $id
     * @return Recipe
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): Recipe;

    public function findByUrl(string $url): ?Recipe;

    public function save(Recipe $recipe): void;

    public function delete(Recipe $recipe): void;
}
