<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends AbstractRepository implements TaskRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /** @inheritDoc */
    public function findOrFail(int $id): Task
    {
        return parent::findOrFailBy($id);
    }

    /** @inheritDoc */
    public function findAll(): array
    {
        return parent::findBy([], ['title' => 'asc']);
    }

    public function save(Task $task): void
    {
        parent::persistAndFlush($task);
    }
}
