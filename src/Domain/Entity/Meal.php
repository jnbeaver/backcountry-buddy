<?php

namespace App\Domain\Entity;

use App\Domain\Enum\MealType;
use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity]
#[ORM\Table(name: 'meals')]
class Meal
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Trip::class, inversedBy: 'meals')]
    #[ORM\JoinColumn(name: 'trip_id')]
    private Trip $trip;

    #[ORM\Column(type: 'date')]
    private DateTime $date;

    #[ORM\Column(type: 'string', enumType: MealType::class)]
    private MealType $type;

    #[ORM\JoinTable(name: 'meal_dishes')]
    #[ORM\JoinColumn(name: 'meal_id')]
    #[ORM\InverseJoinColumn(name: 'dish_id')]
    #[ORM\ManyToMany(targetEntity: Dish::class)]
    private Collection $dishes;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    /**
     * @param Trip $trip
     * @param DateTime $date
     * @param MealType $type
     * @param Dish[] $dishes
     */
    public function __construct(
        Trip $trip,
        DateTime $date,
        MealType $type,
        array $dishes
    ) {
        Assert::allIsInstanceOf($dishes, Dish::class);

        $this->trip = $trip;
        $this->date = $date;
        $this->type = $type;
        $this->dishes = new ArrayCollection($dishes);
        $this->createdAt = $this->updatedAt = Carbon::now();
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getType(): MealType
    {
        return $this->type;
    }

    public function getDishes(): array
    {
        return $this->dishes->toArray();
    }
}
