<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Table(name: 'people')]
#[ORM\UniqueConstraint(columns:['name'])]
class Person implements PersonImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $isChild;

    public function __construct(string $name, bool $isChild)
    {
        $this->name = $name;
        $this->isChild = $isChild;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isAdult(): bool
    {
        return !$this->isChild;
    }

    public function isChild(): bool
    {
        return $this->isChild;
    }
}