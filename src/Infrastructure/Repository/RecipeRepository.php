<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Recipe;
use App\Domain\Repository\RecipeRepositoryInterface;
use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository implements RecipeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /** @inheritDoc */
    public function findOrFail(int $id): Recipe
    {
        $recipe = $this->createQueryBuilder('r')
            ->where('r.id = :id')
            ->andWhere('r.deletedAt IS NULL')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$recipe) {
            throw new EntityNotFoundException($this->class, $id);
        }

        return $recipe;
    }

    /** @inheritDoc */
    public function findByUrl(string $url): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->where('r.url = :url')
            ->andWhere('r.deletedAt IS NULL')
            ->setParameter('url', $url)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(Recipe $recipe): void
    {
        $em = $this->getEntityManager();

        if (!$em->contains($recipe)) {
            $em->persist($recipe);
        }

        $em->flush();
    }

    public function delete(Recipe $recipe): void
    {
        $recipe->delete();

        $this->save($recipe);
    }
}
