<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Trip;
use App\Domain\Repository\TripRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Support\Collection;

class TripRepository extends AbstractRepository implements TripRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findOrFail(int $id): Trip
    {
        return parent::findOrFailBy($id);
    }

    public function findUniqueLocations(): array
    {
        $results = $this->createQueryBuilder('t')
            ->select('DISTINCT t.location')
            ->orderBy('t.location', 'asc')
            ->getQuery()
            ->getResult();

        return (new Collection($results))->pluck('location')->all();
    }

    public function save(Trip $trip): void
    {
        parent::persistAndFlush($trip);
    }

    public function delete(Trip $trip): void
    {
        parent::removeAndFlush($trip);
    }
}
