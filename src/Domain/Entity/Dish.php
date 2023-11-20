<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\DishRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
#[ORM\Table(name: "dishes")]
#[ORM\UniqueConstraint(name: "recipe_id", columns:["recipe_id"])]
class Dish
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\OneToOne(mappedBy: "dish", targetEntity: Recipe::class)]
    private ?Recipe $recipe = null;

    #[ORM\Column(type: "string")]
    private string $title;

    #[ORM\Column(type: "simple_array")]
    private array $ingredients;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime")]
    private DateTime $updatedAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private DateTime $deletedAt;

    /**
     * @param string $title
     * @param string[] $ingredients
     */
    public function __construct(
        string $title,
        array $ingredients
    ) {
        $this->title = $title;
        $this->ingredients = $ingredients;
        $this->createdAt = $this->updatedAt = Carbon::now();
    }

    public static function fromRecipe(Recipe $recipe): self
    {
        $dish = new self($recipe->getTitle(), $recipe->getIngredients());

        $dish->recipe = $recipe;

        return $dish;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRecipe(): ?RecipeImmutable
    {
        return $this->recipe;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
