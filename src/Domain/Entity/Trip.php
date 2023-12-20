<?php

namespace App\Domain\Entity;

use App\Domain\Enum\MealType;
use App\Domain\Enum\TripType;
use App\Infrastructure\Repository\TripRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Table(name: 'trips')]
class Trip implements TripImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', enumType: TripType::class)]
    private TripType $type;

    #[ORM\Column(type: 'string')]
    private string $location;

    #[ORM\Column(type: 'date')]
    private DateTime $startDate;

    #[ORM\Column(type: 'date')]
    private DateTime $endDate;

    #[ORM\Column(type: 'integer')]
    private int $lowTemp;

    #[ORM\Column(type: 'integer')]
    private int $highTemp;

    #[ORM\JoinTable(name: 'trip_attendees')]
    #[ORM\JoinColumn(name: 'trip_id')]
    #[ORM\InverseJoinColumn(name: 'person_id')]
    #[ORM\ManyToMany(targetEntity: Person::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attendees;

    #[ORM\OneToMany(mappedBy: 'trip', targetEntity: Meal::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $meals;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct(
        TripType $type,
        string $location,
        DateTime $startDate,
        DateTime $endDate,
        int $lowTemp,
        int $highTemp,
        array $attendees
    ) {
        Assert::allIsInstanceOf($attendees, Person::class);

        $this->type = $type;
        $this->location = $location;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->lowTemp = $lowTemp;
        $this->highTemp = $highTemp;
        $this->attendees = new ArrayCollection($attendees);
        $this->meals = new ArrayCollection();
        $this->createdAt = Carbon::now();
    }

    public function addMeal(DateTime $date, MealType $type, array $dishes): void
    {
        $date = Carbon::instance($date)->startOfDay();
        $startDate = Carbon::instance($this->startDate)->startOfDay();
        $endDate = Carbon::instance($this->endDate)->startOfDay();

        Assert::true(
            $startDate->lte($date) && $endDate->startOfDay()->gte($date),
            "Date for meal {$date->toDateString()} not within bounds of trip ({$startDate->toDateString()}-{$endDate->toDateString()})."
        );

        Assert::true(
            $this->meals->filter(fn (Meal $meal) => $date->eq($meal->getDate()) && $meal->getType() === $type)->isEmpty(),
            "$type->name for {$date->toDateString()} already exists."
        );

        if (empty($dishes)) {
            return;
        }

        $this->meals->add(new Meal($this, $date, $type, $dishes));

        $this->updatedAt = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): TripType
    {
        return $this->type;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function getLength(): int
    {
        return Carbon::instance($this->endDate)->diffInDays($this->startDate);
    }

    public function getLowTemp(): int
    {
        return $this->lowTemp;
    }

    public function getHighTemp(): int
    {
        return $this->highTemp;
    }

    /**
     * @return PersonImmutable[]
     */
    public function getAttendees(): array
    {
        return $this->attendees->toArray();
    }

    /**
     * @return Meal[]
     */
    public function getMeals(): array
    {
        return $this->meals->toArray();
    }

    public function getDays(): CarbonPeriod
    {
        return CarbonPeriod::create($this->startDate, '1 day', $this->endDate);
    }
}
