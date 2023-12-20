<?php

namespace App\Application\Command;

readonly class DeleteTrip
{
    public function __construct(
        public int $id
    ) {
    }
}
