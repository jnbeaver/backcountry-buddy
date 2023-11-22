<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Person;

interface PersonRepositoryInterface
{
    public function findOrFail(int $id): Person;

    /**
     * @return Person[]
     */
    public function findAll(): array;

    public function save(Person $person): void;
}
