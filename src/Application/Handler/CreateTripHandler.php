<?php

namespace App\Application\Handler;

use App\Application\Command\CreateTrip;
use App\Domain\Entity\Trip;
use App\Domain\Entity\TripImmutable;
use App\Domain\Enum\MealType;
use App\Domain\Repository\RepositoryRegistryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateTripHandler
{
    public function __construct(
        private RepositoryRegistryInterface $repositoryRegistry
    ) {
    }

    public function __invoke(CreateTrip $command): TripImmutable
    {
        $attendees = (new Collection($command->attendeeIds))
            ->map(
                fn (int $id) => $this->repositoryRegistry->getPersonRepository()->findOrFail($id)
            )
            ->all();

        $trip = new Trip(
            $command->type,
            $command->location,
            $command->startDate,
            $command->endDate,
            $command->lowTemp,
            $command->highTemp,
            $attendees
        );

        foreach ($command->mealPlan as $date => $meals) {
            foreach ($meals as $type => $dishIds) {
                $dishes = (new Collection($dishIds))
                    ->map(
                        fn (int $id) => $this->repositoryRegistry->getDishRepository()->findOrFail($id)
                    )
                    ->all();

                $trip->addMeal(Carbon::parse($date), MealType::from($type), $dishes);
            }
        }

        $this->repositoryRegistry->getTripRepository()->save($trip);

        return $trip;
    }
}
