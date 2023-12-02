<?php

namespace App\Application\Services;

use App\Domain\Services\TripCriteriaService as DomainTripCriteriaService;

class TripCriteriaService
{
    public function __construct(
        private readonly DomainTripCriteriaService $tripCriteriaService
    ) {
    }

    public function getTokens(): array
    {
        return DomainTripCriteriaService::TOKENS;
    }

    public function lint(string $expression): ?string
    {
        $this->tripCriteriaService->lint($expression, $errorMessage);

        return $errorMessage;
    }
}
