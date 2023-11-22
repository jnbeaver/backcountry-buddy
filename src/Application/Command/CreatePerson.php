<?php

namespace App\Application\Command;

readonly class CreatePerson
{
    public function __construct(
        public string $name,
        public bool $isChild
    ) {
    }
}
