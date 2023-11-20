<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\RecipeRepository;
use Brick\Schema\Interfaces\HowToStep;
use Brick\Schema\Interfaces\Recipe as RecipeMicrodata;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: "recipes")]
#[ORM\UniqueConstraint(name: "url", columns:["url"])]
class Recipe implements RecipeImmutable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $url;

    #[ORM\Column(type: "string")]
    private string $title;

    #[ORM\Column(type: "simple_array")]
    private array $ingredients;

    #[ORM\Column(type: "simple_array")]
    private array $instructions;

    #[ORM\Column(type: "datetime")]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime")]
    private DateTime $updatedAt;

    #[ORM\Column(type: "datetime", nullable: true)]
    private DateTime $deletedAt;

    public function __construct(
        string $url,
        RecipeMicrodata $microdata
    ) {
        $this->url = $url;
        $this->refresh($microdata);
        $this->createdAt = $this->updatedAt = Carbon::now();
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
    }

    public function delete(): void
    {
        $this->deletedAt = Carbon::now();
    }
}
