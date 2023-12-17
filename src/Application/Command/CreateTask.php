<?php

namespace App\Application\Command;

readonly class CreateTask
{
    public function __construct(
        public string $title,
        public ?string $tripCriteriaExpression
    ) {
    }
}
