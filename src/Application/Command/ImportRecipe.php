<?php

namespace App\Application\Command;

class ImportRecipe
{
    public function __construct(
        private readonly string $url
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
