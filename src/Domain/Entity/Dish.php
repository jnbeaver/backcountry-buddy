<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\DishRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
#[ORM\Table(name: 'dishes')]
#[ORM\UniqueConstraint(columns:['recipe_id'])]
#[ORM\UniqueConstraint(columns:['title'])]
class Dish implements DishImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'dish', targetEntity: Recipe::class)]
    #[ORM\JoinColumn(name: 'recipe_id', referencedColumnName: 'id')]
    private ?Recipe $recipe = null;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $ingredients;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $prep;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTime $deletedAt;

    /**
     * @param string $title
     * @param string[] $ingredients
     * @param string[] $prep
     */
    public function __construct(
        string $title,
        array $ingredients,
        array $prep
    ) {
        $this->title = $title;
        $this->ingredients = $ingredients;
        $this->prep = $prep;
        $this->createdAt = $this->updatedAt = Carbon::now();
    }

    /**
     * @param Recipe $recipe
     * @param string[] $prep
     * @return self
     */
    public static function fromRecipe(Recipe $recipe, array $prep): self
    {
        $dish = new self($recipe->getTitle(), $recipe->getIngredients(), $prep);

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

    /** @inheritDoc */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /** @inheritDoc */
    public function getPrep(): array
    {
        return $this->prep;
    }

    public function refresh(): void
    {
        if (!$this->recipe) {
            return;
        }

        $this->title = $this->recipe->getTitle();
        $this->ingredients = $this->recipe->getIngredients();

        $this->updatedAt = Carbon::now();
    }

    public function delete(): void
    {
        $this->deletedAt = Carbon::now();
    }
}