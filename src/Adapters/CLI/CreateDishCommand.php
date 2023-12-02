<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreateDish;
use App\Application\Command\CreateDishFromRecipe;
use App\Application\Services\GearService;
use App\Domain\Entity\DishImmutable;
use App\Domain\Entity\GearItemImmutable;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bb:dish:create',
    description: 'Creates a new dish, either manually or from an existing recipe.',
)]
class CreateDishCommand extends AbstractDoActionCommand
{
    public function __construct(
        MessageBusInterface $commandBus,
        private readonly GearService $gearService
    ) {
        parent::__construct($commandBus);
    }

    protected function configure(): void
    {
        $this
            ->addOption('recipe', 'r', InputOption::VALUE_OPTIONAL, 'The recipe ID');
    }

    protected function initializeCommand(InputInterface $input, Style $io): CreateDish|CreateDishFromRecipe
    {
        $recipeId = $input->getOption('recipe');

        if ($recipeId !== null) {
            return new CreateDishFromRecipe(
                $recipeId,
                $io->askMany('Preparation Step'),
                $this->askRequiredGear($io)
            );
        }

        return new CreateDish(
            $io->askRequired('Title'),
            $io->askMany('Ingredient'),
            $io->askMany('Preparation Step'),
            $this->askRequiredGear($io)
        );
    }

    protected function getActionStartMessage(): string
    {
        return 'Creating dish...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var DishImmutable $result */
        return "Dish '{$result->getTitle()}' created successfully!";
    }

    /**
     * @param Style $io
     * @return int[]
     */
    private function askRequiredGear(Style $io): array
    {
        return $io->choiceAssoc(
            'Required Gear',
            (new Collection($this->gearService->getAll()))
                ->keyBy(fn (GearItemImmutable $gear) => $gear->getName())
                ->map(fn (GearItemImmutable $gear) => $gear->getId())
                ->all(),
            true,
            true
        ) ?? [];
    }
}
