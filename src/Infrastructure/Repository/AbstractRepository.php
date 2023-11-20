<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param mixed $value
     * @param string $column
     * @return object
     * @throws EntityNotFoundException
     */
    protected function findOrFailBy(mixed $value, string $column = 'ID'): object
    {
        $entity = $this->createQueryBuilder('e')
            ->where(sprintf('e.%s = :value', strtolower($column)))
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$entity) {
            throw new EntityNotFoundException($this->_class, $value, $column);
        }

        return $entity;
    }

    protected function saveOne(object $entity): void
    {
        $em = $this->getEntityManager();

        if (!$em->contains($entity)) {
            $em->persist($entity);
        }

        $em->flush();
    }
}
