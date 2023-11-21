<?php

namespace App\Application\Command;

readonly class RefreshRecipe
{
    public function __construct(
        public string $idOrUrl
    ) {
    }
}
