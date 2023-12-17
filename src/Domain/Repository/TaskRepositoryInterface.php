<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Task;
use App\Infrastructure\Exception\EntityNotFoundException;

interface TaskRepositoryInterface
{
    /**
     * @param int $id
     * @return Task
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): Task;

    /**
     * @return Task[]
     */
    public function findAll(): array;

    public function save(Task $task): void;
}
