<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\Assignee;
use App\Domain\ValueObject\GearInclusionStrategy;
use App\Infrastructure\Repository\GearItemRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GearItemRepository::class)]
#[ORM\Table(name: 'gear')]
#[ORM\UniqueConstraint(columns:['name'])]
class GearItem implements GearItemImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Embedded(class: GearInclusionStrategy::class, columnPrefix: 'inclusion_')]
    private GearInclusionStrategy $inclusionStrategy;

    #[ORM\Embedded(class: Assignee::class)]
    private ?Assignee $assignee;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct(
        string $name,
        GearInclusionStrategy $inclusionStrategy,
        ?Assignee $assignee
    ) {
        $this->name = $name;
        $this->inclusionStrategy = $inclusionStrategy;
        $this->assignee = $assignee;
        $this->createdAt = $this->updatedAt = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInclusionStrategy(): GearInclusionStrategy
    {
        return $this->inclusionStrategy;
    }

    public function getAssignee(): ?Assignee
    {
        if ($this->assignee === null || $this->assignee->isNull()) {
            return null;
        }

        return $this->assignee;
    }
}
