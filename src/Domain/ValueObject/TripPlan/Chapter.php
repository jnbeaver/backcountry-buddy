<?php

namespace App\Domain\ValueObject\TripPlan;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

readonly class Chapter
{
    public function __construct(
        private array $sections
    ) {
        Assert::allIsInstanceOf($sections, Section::class);
    }

    public function getContent(): ?string
    {
        $sections = (new Collection($this->sections))
            ->map(fn (Section $section) => $section->getContent())
            ->filter(fn (?string $content) => $content !== null);

        if ($sections->isEmpty()) {
            return null;
        }

        return $sections->join("\n\n");
    }
}
