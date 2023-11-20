<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\RecipeRepository;
use Brick\Schema\Interfaces\HowToStep;
use Brick\Schema\Interfaces\Recipe as RecipeMicrodata;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: "recipes")]
#[ORM\UniqueConstraint(name: "url", columns:["url"])]
class Recipe
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $url;

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

    private function __construct()
    {
        $this->createdAt = $this->updatedAt = Carbon::now();
    }

    public static function fromWebpageMicrodata(string $url, RecipeMicrodata $microdata): self
    {
        $recipe = new self();

        $recipe->url = $url;
        $recipe->refresh($microdata);

        return $recipe;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @return string[]
     */
    public function getInstructions(): array
    {
        return $this->instructions;
    }

    public function refresh(RecipeMicrodata $microdata): void
    {
        Assert::notNull($this->url, 'Only recipes imported from a webpage can be refreshed.');

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
