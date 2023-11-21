<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\RecipeRepository;
use Brick\Schema\Interfaces\HowToStep;
use Brick\Schema\Interfaces\Recipe as RecipeMicrodata;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: 'recipes')]
#[ORM\UniqueConstraint(columns:['url'])]
#[ORM\UniqueConstraint(columns:['title'])]
class Recipe implements RecipeImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(mappedBy: 'recipe', targetEntity: Dish::class, cascade: ['persist'])]
    private ?Dish $dish = null;

    #[ORM\Column(type: 'string')]
    private string $url;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $ingredients;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $instructions;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTime $deletedAt;

    public function __construct(
        string $url,
        RecipeMicrodata $microdata
    ) {
        $this->url = $url;
        $this->refresh($microdata);
        $this->createdAt = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
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
    public function getInstructions(): array
    {
        return $this->instructions;
    }

    public function createDish(): Dish
    {
        return $this->dish = Dish::fromRecipe($this);
    }

    public function refresh(RecipeMicrodata $microdata): void
    {
        $this->title = $microdata->name;
        $this->ingredients = $microdata->ingredients->count() > 0 ? $microdata->ingredients->getValues() : $microdata->recipeIngredient->getValues();

        $this->instructions = [];

        foreach ($microdata->recipeInstructions as $step) {
            if ($step instanceof HowToStep) {
                $this->instructions[] = $step->text->getFirstValue();
            }
        }

        if ($this->dish) {
            $this->dish->refresh();
        }

        $this->updatedAt = Carbon::now();
    }

    public function delete(): void
    {
        $this->deletedAt = Carbon::now();

        $this->dish?->delete();
    }
}
