<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\GearItem;
use App\Domain\Repository\GearItemRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class GearItemRepository extends AbstractRepository implements GearItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GearItem::class);
    }

    /** @inheritDoc */
    public function findOrFail(int $id): GearItem
    {
        return parent::findOrFailBy($id);
    }

    /** @inheritDoc */
    public function findAll(): array
    {
        return parent::findBy([], ['name' => 'asc']);
    }

    public function save(GearItem $gearItem): void
    {
        parent::persistAndFlush($gearItem);
    }
}
