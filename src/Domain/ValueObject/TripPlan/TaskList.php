<?php

namespace App\Domain\ValueObject\TripPlan;

use App\Component\Markdown\Markdown;
use App\Domain\Entity\TaskImmutable;
use App\Domain\Entity\TripImmutable;
use App\Domain\Services\TripCriteriaService;
use Illuminate\Support\Collection;

readonly class TaskList implements Section
{
    /**
     * @param TripImmutable $trip
     * @param TaskImmutable[] $tasks
     * @param TripCriteriaService $tripCriteriaService
     */
    public function __construct(
        private TripImmutable $trip,
        private array $tasks,
        private TripCriteriaService $tripCriteriaService
    ) {
    }

    public function getContent(): ?string
    {
        $tasks = (new Collection($this->tasks))
            ->filter(function (TaskImmutable $task) {
                $tripCriteria = $task->getTripCriteria();

                if (!$tripCriteria) {
                    return true;
                }

                return $this->tripCriteriaService->evaluate($tripCriteria, $this->trip);
            })
            ->map(fn (TaskImmutable $task) => $task->getTitle());

        if ($tasks->isEmpty()) {
            return null;
        }

        return sprintf(
            "%s\n%s\n",
            Markdown::header2('Task List'),
            Markdown::tasklist($tasks->all())
        );
    }
}
