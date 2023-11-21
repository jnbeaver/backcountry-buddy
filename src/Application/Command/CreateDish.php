<?php

namespace App\Application\Command;

readonly class CreateDish
{
    /**
     * @param string $title
     * @param string[] $ingredients
     */
    public function __construct(
        public string $title,
        public array $ingredients,
    ) {
    }
}
