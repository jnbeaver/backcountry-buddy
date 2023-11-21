<?php

namespace App\Application\Command;

readonly class CreateDish
{
    /**
     * @param string $title
     * @param string[] $ingredients
     * @param string[] $prep
     */
    public function __construct(
        public string $title,
        public array $ingredients,
        public array $prep
    ) {
    }
}
