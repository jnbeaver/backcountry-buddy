<?php

namespace App\Application\Command;

readonly class ImportRecipe
{
    public function __construct(
        public string $url
    ) {
    }
}
