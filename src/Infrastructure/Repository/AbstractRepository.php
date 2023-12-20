<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param mixed $value
     * @param string $property
     * @param string|null $name
     * @return object
     * @throws EntityNotFoundException
     */
    protected function findOrFailBy(
        mixed $value,
        string $property = 'id',
        ?string $name = 'ID'
    ): object {
        if ($property === 'id') {
            $entity = $this->find($value);
        } else {
            $entity = $this->findOneBy([$property => $value]);
        }

        if (!$entity) {
            throw new EntityNotFoundException(
                $this->getClassMetadata()->getName(),
                $value,
                $name ?? $property
            );
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

    protected function removeAndFlush(object $entity): void
    {
        $em = $this->getEntityManager();

        $em->remove($entity);
        $em->flush();
    }
}
