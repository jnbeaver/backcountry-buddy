<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Person;
use App\Infrastructure\Exception\EntityNotFoundException;

interface PersonRepositoryInterface
{
    /**
     * @param int $id
     * @return Person
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): Person;

    /**
     * @return Person[]
     */
    public function findAll(): array;

    public function save(Person $person): void;
}
