<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\TripCriteria;
use App\Infrastructure\Repository\TaskRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'tasks')]
#[ORM\UniqueConstraint(columns:['title'])]
class Task implements TaskImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Embedded(class: TripCriteria::class, columnPrefix: false)]
    private ?TripCriteria $tripCriteria;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct(
        string $title,
        ?TripCriteria $tripCriteria
    ) {
        $this->title = $title;
        $this->tripCriteria = $tripCriteria;
        $this->createdAt = $this->updatedAt = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTripCriteria(): ?TripCriteria
    {
        return $this->tripCriteria;
    }
}
