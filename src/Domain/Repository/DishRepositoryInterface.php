<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Dish;
use App\Infrastructure\Exception\EntityNotFoundException;

interface DishRepositoryInterface
{
    /**
     * @param int $id
     * @return Dish
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): Dish;

    /**
     * @return Dish[]
     */
    public function findAll(): array;

    public function save(Dish $dish): void;
}
