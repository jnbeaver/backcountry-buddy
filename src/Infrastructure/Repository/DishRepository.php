<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Dish;
use App\Domain\Repository\DishRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class DishRepository extends AbstractRepository implements DishRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    /** @inheritDoc */
    public function findOrFail(int $id): Dish
    {
        return parent::findOrFailBy($id);
    }

    /** @inheritDoc */
    public function findAll(): array
    {
        return parent::findBy([], ['title' => 'asc']);
    }

    public function save(Dish $dish): void
    {
        parent::persistAndFlush($dish);
    }
}
