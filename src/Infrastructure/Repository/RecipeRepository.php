<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Recipe;
use App\Domain\Repository\RecipeRepositoryInterface;
use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends AbstractRepository implements RecipeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /** @inheritDoc */
    public function findOrFail(int $id): Recipe
    {
        return parent::findOrFailBy($id);
    }

    /** @inheritDoc */
    public function findOrFailByUrl(string $url): Recipe
    {
        return parent::findOrFailBy($url, 'url', 'URL');
    }

    public function save(Recipe $recipe): void
    {
        parent::persistAndFlush($recipe);
    }
}
