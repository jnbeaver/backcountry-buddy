<?php

namespace App\Adapters\CLI;

use App\Application\Command\CreatePerson;
use App\Domain\Entity\PersonImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;

#[AsCommand(
    name: 'bb:person:create',
    description: 'Creates a new person.',
)]
class CreatePersonCommand extends AbstractDoActionCommand
{
    protected function initializeCommand(InputInterface $input, Style $io): CreatePerson
    {
        return new CreatePerson(
            $io->askRequired('Name'),
            $io->confirm('Are they a child?', false)
        );
    }

    protected function getActionStartMessage(): string
    {
        return 'Creating person...';
    }

    protected function getActionSuccessMessage(object $result): string
    {
        /** @var PersonImmutable $result */
        return "{$result->getName()} was created successfully!";
    }
}
