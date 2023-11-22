<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Person;
use App\Domain\Repository\PersonRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class PersonRepository extends AbstractRepository implements PersonRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function findOrFail(int $id): Person
    {
        return parent::findOrFailBy($id);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(Person $person): void
    {
        parent::persistAndFlush($person);
    }
}