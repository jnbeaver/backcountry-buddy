<?php

namespace App\Application\Command;

class RefreshRecipe implements CommandInterface
{
    public function __construct(
        private readonly string $idOrUrl
    ) {
    }

    public function getIdOrUrl(): string
    {
        return $this->idOrUrl;
    }
}
