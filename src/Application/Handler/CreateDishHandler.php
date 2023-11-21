<?php

namespace App\Application\Handler;

use App\Application\Command\CreateDish;
use App\Domain\Entity\Dish;
use App\Domain\Entity\DishImmutable;
use App\Domain\Repository\RepositoryRegistryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateDishHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    public function __invoke(CreateDish $command): DishImmutable
    {
        $dish = new Dish($command->title, $command->ingredients, $command->prep);

        $this->repositoryRegistry->getDishRepository()->save($dish);

        return $dish;
    }
}
