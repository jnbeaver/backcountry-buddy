<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateTrip;
use App\Application\Services\DishService;
use App\Application\Services\PersonService;
use App\Application\Services\TripService;
use App\Domain\Entity\DishImmutable;
use App\Domain\Entity\PersonImmutable;
use App\Domain\Entity\TripImmutable;
use App\Domain\Enum\MealType;
use App\Domain\Enum\TripType;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bb:trip:create',
    description: 'Creates a new trip.',
)]
class CreateTripCommand extends AbstractDoActionCommand
{
    public function __construct(
        MessageBusInterface $commandBus,
        private readonly TripService $tripService,
        private readonly PersonService $personService,
        private readonly DishService $dishService
    ) {
        parent::__construct($commandBus);
    }

    protected function initializeCommand(InputInterface $input, Style $io): object
    {
        $io->section('Itinerary');

        $type = ($tripTypes = TripType::asMap())[$io->choice('Type', array_keys($tripTypes))];
        $location = $io->askRequired('Location', $this->tripService->getExistingTripLocations());
        $startDate = $io->askDate('Start Date');
        $endDate = $io->askDate('End Date', $startDate);

        $io->section('Attendees');

        $attendeeIds = (new Collection($this->personService->getAll()))
            ->filter(fn (PersonImmutable $person) => $io->confirm($person->getName()))
            ->map(fn (PersonImmutable $person) => $person->getId())
            ->all();

        $io->section('Weather Forecast');

        $lowTemp = $io->askInteger('Low Temperature');
        $highTemp = $io->askInteger('High Temperature', $lowTemp);

        $io->section('Meal Plan');

        $dishes = (new Collection($this->dishService->getAll()))
            ->keyBy(fn (DishImmutable $dish) => $dish->getTitle())
            ->map(fn (DishImmutable $dish) => $dish->getId());

        $mealPlan = [];

        foreach (CarbonPeriod::create($startDate, '1 day', $endDate) as $date) {
            foreach (MealType::cases() as $mealType) {
                $question = new ChoiceQuestion(
                    sprintf('%s %s', $date->format('n/j/y'), $mealType->name),
                    $dishes->keys()->all()
                );

                $question->setMultiselect(true);

                // override default validator to handle empty answers
                $defaultValidator = $question->getValidator();
                $question->setValidator(function (?string $answer) use ($defaultValidator) {
                    if ($answer === null || $answer === '') {
                        return [];
                    }

                    return $defaultValidator($answer);
                });

                $dishNames = $io->askQuestion($question);

                $mealPlan[$date->format('Y-m-d')][$mealType->value] = $dishes
                    ->intersectByKeys((new Collection($dishNames))->flip())
                    ->values()
                    ->all();
            }
        }

        return new CreateTrip($type, $location, $startDate, $endDate, $attendeeIds, $lowTemp, $highTemp, $mealPlan);
    }

    protected function getActionStartMessage(): string
    {
        return 'Creating trip...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var TripImmutable $result */
        return sprintf(
            "Trip to %s from %s to %s created successfully!",
            $result->getLocation(),
            $result->getStartDate()->format('n/j/y'),
            $result->getEndDate()->format('n/j/y')
        );
    }
}
