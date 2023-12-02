<?php

namespace App\Domain\Repository;

use App\Domain\Entity\GearItem;
use App\Infrastructure\Exception\EntityNotFoundException;

interface GearItemRepositoryInterface
{
    /**
     * @param int $id
     * @return GearItem
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): GearItem;

    /**
     * @return GearItem[]
     */
    public function findAll(): array;

    public function save(GearItem $gearItem): void;
}
