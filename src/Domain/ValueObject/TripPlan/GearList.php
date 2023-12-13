<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\DishImmutable;
use App\Domain\Entity\GearItemImmutable;
use App\Domain\Entity\Meal;
use App\Domain\Entity\PersonImmutable;
use App\Domain\Entity\TripImmutable;
use App\Domain\Enum\AssigneeType;
use App\Domain\Enum\GearInclusionFrequency;
use App\Domain\Services\TripCriteriaService;
use Illuminate\Support\Collection;

readonly class GearList implements Section
{
    /**
     * @param TripImmutable $trip
     * @param GearItemImmutable[] $gear
     * @param TripCriteriaService $tripCriteriaService
     */
    public function __construct(
        private TripImmutable $trip,
        private array $gear,
        private TripCriteriaService $tripCriteriaService
    ) {
    }

    public function getContent(): ?string
    {
        $attendees = (new Collection($this->trip->getAttendees()))
            ->keyBy(fn (PersonImmutable $attendee) => $attendee->getId());

        $gear = (new Collection($this->gear))
            ->filter(function (GearItemImmutable $gearItem) {
                $inclusionStrategy = $gearItem->getInclusionStrategy();

                return match ($inclusionStrategy->getFrequency()) {
                    GearInclusionFrequency::Always => true,
                    GearInclusionFrequency::Sometimes => $this->tripCriteriaService->evaluate(
                        $inclusionStrategy->getTripCriteria(),
                        $this->trip
                    ),
                    default => false, // only for meal prep
                };
            })
            ->keyBy(fn (GearItemImmutable $gearItem) => $gearItem->getId())
            ->merge(
                (new Collection($this->trip->getMeals()))
                    ->map(fn (Meal $meal) => $meal->getDishes())
                    ->flatten(1)
                    ->map(fn (DishImmutable $dish) => $dish->getRequiredGear())
                    ->flatten(1)
                    ->keyBy(fn (GearItemImmutable $gearItem) => $gearItem->getId())
            )
            ->sortBy(fn (GearItemImmutable $gearItem) => $gearItem->getName());

        if ($gear->isEmpty()) {
            return null;
        }

        [$unassignedGear, $assignedGear] = $gear->partition(fn (GearItemImmutable $gearItem) => $gearItem->getAssignee() === null);

        $gearByAttendee = $attendees->map(fn () => new Collection());

        $assignedGear
            ->each(function (GearItemImmutable $gearItem) use ($attendees, $gearByAttendee) {
                $assignee = $gearItem->getAssignee();

                $attendees
                    ->filter(function (PersonImmutable $attendee) use ($assignee) {
                        return match($assignee->getType()) {
                            AssigneeType::Adults => $attendee->isAdult(),
                            AssigneeType::Children => $attendee->isChild(),
                            AssigneeType::Individual => $attendee->getId() === $assignee->getPersonId(),
                            default => true, // all
                        };
                    })
                    ->keys()
                    ->each(fn (int $attendeeId) => $gearByAttendee->get($attendeeId)->push($gearItem));
            });

        $gearByAttendee = $gearByAttendee->filter(fn (Collection $gear) => $gear->isNotEmpty());

        $content = sprintf("%s\n", Markdown::header2('Gear List'));

        if ($unassignedGear->isNotEmpty()) {
            $content .= sprintf(
                "%s\n",
                Markdown::tasklist(
                    $unassignedGear->map(fn (GearItemImmutable $gearItem) => $gearItem->getName())->all()
                )
            );
        }

        if ($gearByAttendee->isNotEmpty()) {
            $content .= sprintf(
                "%s\n",
                $gearByAttendee
                    ->map(function (Collection $gear, int $attendeeId) use ($attendees) {
                        $attendee = $attendees->get($attendeeId);

                        return sprintf(
                            "%s\n%s\n",
                            Markdown::bold($attendee->getName()),
                            Markdown::tasklist(
                                $gear->map(fn (GearItemImmutable $gearItem) => $gearItem->getName())->all()
                            )
                        );
                    })
                    ->join("\n")
            );
        }

        return $content;
    }
}
