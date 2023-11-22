<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param mixed $value
     * @param string $property
     * @param string|null $column
     * @return object
     * @throws EntityNotFoundException
     */
    protected function findOrFailBy(
        mixed $value,
        string $property = 'id',
        ?string $name = 'ID'
    ): object {
        $entity = $this->createQueryBuilder('e')
            ->where(sprintf('e.%s = :value', $property))
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$entity) {
            throw new EntityNotFoundException($this->_class, $value, $name ?? $property);
        }

        return $entity;
    }

    protected function persistAndFlush(object $entity): void
    {
        $em = $this->getEntityManager();

        if (!$em->contains($entity)) {
            $em->persist($entity);
        }

        $em->flush();
    }
}
