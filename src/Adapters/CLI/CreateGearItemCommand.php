<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateGearItem;
use App\Application\Services\PersonService;
use App\Application\Services\TripCriteriaService;
use App\Domain\Entity\GearItemImmutable;
use App\Domain\Entity\PersonImmutable;
use App\Domain\Enum\AssigneeType;
use App\Domain\Enum\GearInclusionFrequency;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bb:gear:create',
    description: 'Creates a new gear item.',
)]
class CreateGearItemCommand extends AbstractDoActionCommand
{
    public function __construct(
        MessageBusInterface $commandBus,
        private readonly TripCriteriaService $tripCriteriaService,
        private readonly PersonService $personService
    ) {
        parent::__construct($commandBus);
    }

    protected function initializeCommand(InputInterface $input, Style $io): CreateGearItem
    {
        $name = $io->askRequired('Name');
        $inclusionFrequency = $io->choiceAssoc('Include this Gear Item on a Trip', GearInclusionFrequency::asChoice());

        $tripCriteriaExpression = null;

        if ($inclusionFrequency === GearInclusionFrequency::Sometimes) {
            $question = new Question('Trip Criteria Expression');

            $question->setValidator(function (?string $answer) {
                if (empty($answer)) {
                    throw new RuntimeException('This value is required.');
                }

                $errorMessage = $this->tripCriteriaService->lint($answer);

                if ($errorMessage !== null) {
                    throw new RuntimeException("Invalid expression: $errorMessage");
                }

                return $answer;
            });

            $tripCriteriaExpression = $io->askQuestion($question);
        }

        $assigneeType = null;
        $assigneeId = null;

        if ($inclusionFrequency !== GearInclusionFrequency::OnlyWhenRequiredForMealPrep) {
            $assigneeType = $io->choiceAssoc('Assign to Trip Attendees', AssigneeType::asChoice(), true);

            if ($assigneeType === AssigneeType::Individual) {
                $assigneeId = $io->choiceAssoc(
                    'Assign to Person',
                    (new Collection($this->personService->getAll()))
                        ->keyBy(fn(PersonImmutable $person) => $person->getName())
                        ->map(fn(PersonImmutable $person) => $person->getId())
                        ->all()
                );
            }
        }

        return new CreateGearItem($name, $inclusionFrequency, $tripCriteriaExpression, $assigneeType, $assigneeId);
    }

    protected function getActionStartMessage(): string
    {
        return 'Creating gear item...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var GearItemImmutable $result */
        return "Gear item '{$result->getName()}' created successfully!";
    }
}
