<?php

namespace App\Application\Command;

readonly class PlanTrip
{
    public function __construct(
        public int $id,
        public string $filename
    ) {
    }
}
